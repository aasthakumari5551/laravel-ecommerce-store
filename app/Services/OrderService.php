<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CartService    $cartService,
        private PaymentGateway $gateway,         // injected — Simulated or Real
    ) {}

    // ── Step 1: Initiate checkout ─────────────────────────────

    public function initiate(int $addressId, ?string $notes = null): array
    {
        $user    = Auth::user();
        $address = Address::where('user_id', $user->id)->findOrFail($addressId);
        $cart    = $this->cartService->resolve()->load('items.product');

        if ($cart->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        foreach ($cart->items as $item) {
            $this->assertStock($item->product, $item->quantity);
        }

        return DB::transaction(function () use ($user, $cart, $address, $notes) {

            // Deduct stock with row-level lock — prevents overselling
            foreach ($cart->items as $item) {
                Product::where('id', $item->product_id)
                       ->where('track_inventory', true)
                       ->lockForUpdate()
                       ->decrement('stock', $item->quantity);
            }

            $subtotal = $cart->subtotal();
            $shipping = $this->calculateShipping($subtotal);
            $tax      = $this->calculateTax($subtotal);
            $total    = $subtotal + $shipping + $tax;

            $order = Order::create([
                'uuid'                => (string) Str::uuid(),
                'number'              => $this->generateOrderNumber(),
                'user_id'             => $user->id,
                'shipping_first_name' => $address->first_name,
                'shipping_last_name'  => $address->last_name,
                'shipping_phone'      => $address->phone,
                'shipping_line1'      => $address->line1,
                'shipping_line2'      => $address->line2,
                'shipping_city'       => $address->city,
                'shipping_state'      => $address->state,
                'shipping_pincode'    => $address->pincode,
                'shipping_country'    => $address->country,
                'subtotal'            => $subtotal,
                'shipping_amount'     => $shipping,
                'tax_amount'          => $tax,
                'total'               => $total,
                'status'              => OrderStatus::Pending,
                'payment_status'      => PaymentStatus::Pending,
                'payment_method'      => 'demo_gateway',
                'notes'               => $notes,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku'  => $item->product->sku,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'subtotal'     => $item->lineTotal(),
                ]);
            }

            $this->logStatus($order, OrderStatus::Pending, 'Order created — awaiting payment.');

            // Gateway call — works identically for demo or real Razorpay
            $gatewayOrder = $this->gateway->createOrder($total, $order->number);

            $order->update(['razorpay_order_id' => $gatewayOrder['id']]);

            return [
                'order'         => $order,
                'gateway_order' => $gatewayOrder,
                'public_key'    => $this->gateway->getPublicKey(),
            ];
        });
    }

    // ── Step 2: Verify and confirm ────────────────────────────

    public function verifyAndConfirm(
        string $gatewayOrderId,
        string $gatewayPaymentId,
        string $gatewaySignature,
    ): Order {
        // Throws on bad signature — same contract for demo and real
        $this->gateway->verifySignature($gatewayOrderId, $gatewayPaymentId, $gatewaySignature);

        $order = Order::where('razorpay_order_id', $gatewayOrderId)->firstOrFail();

        return DB::transaction(function () use ($order, $gatewayPaymentId, $gatewaySignature) {
            $order->update([
                'payment_status'      => PaymentStatus::Paid,
                'status'              => OrderStatus::Confirmed,
                'razorpay_payment_id' => $gatewayPaymentId,
                'razorpay_signature'  => $gatewaySignature,
                'paid_at'             => now(),
            ]);

            $this->logStatus($order, OrderStatus::Confirmed, 'Payment verified — order confirmed.');
            $this->cartService->clear();

            return $order->fresh();
        });
    }

    // ── Step 3: Handle payment failure ───────────────────────

    public function handlePaymentFailure(string $gatewayOrderId): void
    {
        $order = Order::where('razorpay_order_id', $gatewayOrderId)->first();

        if (! $order || $order->payment_status === PaymentStatus::Paid) {
            return;
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

    // ── Admin: Transition status ──────────────────────────────

    public function transitionStatus(Order $order, OrderStatus $newStatus, ?string $comment = null): Order
    {
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

            $this->logStatus($order, $newStatus, $comment);
            return $order->fresh();
        });
    }

    // ── Private helpers ───────────────────────────────────────

    private function assertStock(Product $product, int $qty): void
    {
        if ($product->track_inventory && $product->stock < $qty) {
            throw new \RuntimeException(
                "\"{$product->name}\" has only {$product->stock} unit(s) in stock."
            );
        }
    }

    private function restoreStock(Order $order): void
    {
        foreach ($order->items as $item) {
            Product::where('id', $item->product_id)
                   ->where('track_inventory', true)
                   ->increment('stock', $item->quantity);
        }
    }

    private function logStatus(Order $order, OrderStatus $status, ?string $comment = null): void
    {
        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'status'     => $status,
            'comment'    => $comment,
            'changed_by' => Auth::id(),
        ]);
    }

    private function generateOrderNumber(): string
    {
        $prefix   = 'ORD-' . now()->format('Ymd') . '-';
        $last     = Order::where('number', 'like', $prefix . '%')->orderByDesc('number')->value('number');
        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    private function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 999 ? 0.0 : 99.0;
    }

    private function calculateTax(float $subtotal): float
    {
        return round($subtotal * 0.18, 2);
    }
}