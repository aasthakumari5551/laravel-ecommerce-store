<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CheckoutService $checkoutService,
        private CartService     $cartService,
        private PaymentGateway  $gateway,
    ) {}

    // ─────────────────────────────────────────────────────────
    // PUBLIC API
    // ─────────────────────────────────────────────────────────

    /**
     * Validate cart + address, deduct stock, persist order,
     * create gateway order. Returns data for payment screen.
     *
     * @throws \RuntimeException
     */
    public function initiate(int $addressId, ?string $notes = null): array
    {
        // Delegate all validation to CheckoutService
        $validated = $this->checkoutService->validate($addressId);
        $totals    = $this->checkoutService->calculateTotals($validated['subtotal']);

        return DB::transaction(function () use ($validated, $totals, $notes) {
            $cart    = $validated['cart'];
            $address = $validated['address'];
            $user    = Auth::user();

            // ── Lock rows and deduct stock atomically ─────────
            foreach ($cart->items as $item) {
                if (! $item->product->track_inventory) {
                    continue;
                }

                $affected = Product::where('id', $item->product_id)
                                   ->where('track_inventory', true)
                                   ->where('stock', '>=', $item->quantity) // final guard
                                   ->lockForUpdate()
                                   ->decrement('stock', $item->quantity);

                // Race condition: another request deducted stock between validate() and lock
                if ($affected === 0) {
                    throw new \RuntimeException(
                        "\"{$item->product->name}\" went out of stock. Please update your cart."
                    );
                }
            }

            // ── Create order ──────────────────────────────────
            $order = Order::create([
                'uuid'                => (string) Str::uuid(),
                'number'              => $this->generateOrderNumber(),
                'user_id'             => $user->id,

                // Snapshot address — survives future address edits
                'shipping_first_name' => $address->first_name,
                'shipping_last_name'  => $address->last_name,
                'shipping_phone'      => $address->phone,
                'shipping_line1'      => $address->line1,
                'shipping_line2'      => $address->line2,
                'shipping_city'       => $address->city,
                'shipping_state'      => $address->state,
                'shipping_pincode'    => $address->pincode,
                'shipping_country'    => $address->country,

                ...$totals,

                'status'          => OrderStatus::Pending,
                'payment_status'  => PaymentStatus::Pending,
                'payment_method'  => 'demo_gateway',
                'notes'           => $notes,
            ]);

            // ── Snapshot order items ──────────────────────────
            $this->createOrderItems($order, $cart->items);

            // ── Log initial status ────────────────────────────
            $this->logStatus($order, OrderStatus::Pending, 'Order created — awaiting payment.');

            // ── Create gateway order ──────────────────────────
            $gatewayOrder = $this->gateway->createOrder($totals['total'], $order->number);
            $order->update(['razorpay_order_id' => $gatewayOrder['id']]);

            return [
                'order'         => $order->fresh(),
                'gateway_order' => $gatewayOrder,
                'public_key'    => $this->gateway->getPublicKey(),
            ];
        });
    }

    /**
     * Verify payment signature → mark paid → confirm order → clear cart.
     *
     * @throws \RuntimeException on bad signature or order not found
     */
    public function verifyAndConfirm(
        string $gatewayOrderId,
        string $gatewayPaymentId,
        string $gatewaySignature,
    ): Order {
        $this->gateway->verifySignature($gatewayOrderId, $gatewayPaymentId, $gatewaySignature);

        $order = Order::where('razorpay_order_id', $gatewayOrderId)
                      ->where('payment_status', PaymentStatus::Pending->value)
                      ->firstOrFail();

        return DB::transaction(function () use ($order, $gatewayPaymentId, $gatewaySignature) {
            $order->update([
                'payment_status'      => PaymentStatus::Paid,
                'status'              => OrderStatus::Confirmed,
                'razorpay_payment_id' => $gatewayPaymentId,
                'razorpay_signature'  => $gatewaySignature,
                'paid_at'             => now(),
            ]);

            $this->logStatus($order, OrderStatus::Confirmed, 'Payment verified — order confirmed.');

            // Clear the cart after successful payment
            $this->cartService->clear();

            return $order->fresh(['items', 'user']);
        });
    }

    /**
     * Payment failed or was cancelled — restore stock, cancel order.
     */
    public function handlePaymentFailure(string $gatewayOrderId): void
    {
        $order = Order::where('razorpay_order_id', $gatewayOrderId)
                      ->where('payment_status', PaymentStatus::Pending->value)
                      ->first();

        if (! $order) {
            return; // Already handled or doesn't exist
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => PaymentStatus::Failed,
                'status'         => OrderStatus::Cancelled,
            ]);

            $this->restoreStock($order);
            $this->logStatus($order, OrderStatus::Cancelled, 'Payment failed — stock restored.');
        });
    }

    /**
     * Customer-initiated cancellation (only allowed from Pending/Confirmed).
     *
     * @throws \RuntimeException if order cannot be cancelled
     */
    public function cancelByCustomer(Order $order): Order
    {
        if (! $order->status->canTransitionTo(OrderStatus::Cancelled)) {
            throw new \RuntimeException(
                "This order cannot be cancelled at its current stage ({$order->status->label()})."
            );
        }

        return DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::Cancelled]);

            // Restore stock if payment had gone through
            if ($order->isPaid()) {
                $this->restoreStock($order);
            }

            $this->logStatus(
                $order,
                OrderStatus::Cancelled,
                'Cancelled by customer.',
            );

            return $order->fresh();
        });
    }

    /**
     * Admin-driven status transition — enforces the state machine.
     *
     * @throws \RuntimeException on illegal transition
     */
    public function transitionStatus(
        Order $order,
        OrderStatus $newStatus,
        ?string $comment = null,
    ): Order {
        if (! $order->status->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Cannot transition from [{$order->status->label()}] to [{$newStatus->label()}]."
            );
        }

        return DB::transaction(function () use ($order, $newStatus, $comment) {
            $order->update(['status' => $newStatus]);

            if ($newStatus === OrderStatus::Cancelled && $order->isPaid()) {
                $this->restoreStock($order);
            }

            $this->logStatus($order, $newStatus, $comment, Auth::id());

            return $order->fresh();
        });
    }

    // ─────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────

    private function createOrderItems(Order $order, $cartItems): void
    {
        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,  // snapshot
                'product_sku'  => $item->product->sku,   // snapshot
                'quantity'     => $item->quantity,
                'unit_price'   => $item->unit_price,      // price locked at add-to-cart time
                'subtotal'     => $item->lineTotal(),
            ]);
        }
    }

    private function restoreStock(Order $order): void
    {
        // Load items if not already loaded
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            Product::where('id', $item->product_id)
                   ->where('track_inventory', true)
                   ->increment('stock', $item->quantity);
        }
    }

    private function logStatus(
        Order $order,
        OrderStatus $status,
        ?string $comment = null,
        ?int $changedBy = null,
    ): void {
        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $status,
            'comment'    => $comment,
            'changed_by' => $changedBy ?? Auth::id(),
        ]);
    }

    private function generateOrderNumber(): string
    {
        $prefix   = 'ORD-' . now()->format('Ymd') . '-';
        $last     = Order::where('number', 'like', $prefix . '%')
                         ->orderByDesc('number')
                         ->value('number');
        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}