<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CheckoutService
{
    public function __construct(private CartService $cartService) {}

    /**
     * Full pre-checkout validation pipeline.
     * Returns structured data ready for OrderService::initiate().
     *
     * @throws \RuntimeException with user-facing message on any failure
     */
    public function validate(int $addressId): array
    {
        $user = Auth::user();

        // ── 1. Cart must exist and be non-empty ───────────────
        $cart = $this->cartService->resolve()->load('items.product');

        if ($cart->isEmpty()) {
            throw new \RuntimeException('Your cart is empty.');
        }

        // ── 2. Address must belong to this user ───────────────
        $address = Address::where('user_id', $user->id)
                          ->find($addressId);

        if (! $address) {
            throw new \RuntimeException('Selected delivery address is invalid.');
        }

        // ── 3. All products must still be active ──────────────
        $inactiveItems = $cart->items->filter(
            fn ($item) => ! $item->product->is_active || $item->product->trashed()
        );

        if ($inactiveItems->isNotEmpty()) {
            $names = $inactiveItems->pluck('product.name')->implode(', ');
            throw new \RuntimeException(
                "The following items are no longer available: {$names}. Please remove them from your cart."
            );
        }

        // ── 4. Stock check for every item ─────────────────────
        $stockErrors = [];

        foreach ($cart->items as $item) {
            if (! $item->product->track_inventory) {
                continue;
            }

            if ($item->product->stock < $item->quantity) {
                $available = $item->product->stock;
                $stockErrors[] = $available > 0
                    ? "\"{$item->product->name}\": only {$available} left (you have {$item->quantity} in cart)."
                    : "\"{$item->product->name}\" is out of stock.";
            }
        }

        if (! empty($stockErrors)) {
            throw new \RuntimeException(implode(' ', $stockErrors));
        }

        // ── 5. Return validated payload ───────────────────────
        return [
            'cart'     => $cart,
            'address'  => $address,
            'subtotal' => $cart->subtotal(),
        ];
    }

    /**
     * Calculate order financials from a validated subtotal.
     */
    public function calculateTotals(float $subtotal): array
    {
        $shipping = $this->shippingRate($subtotal);
        $tax      = $this->taxRate($subtotal);

        return [
            'subtotal'        => $subtotal,
            'shipping_amount' => $shipping,
            'tax_amount'      => $tax,
            'total'           => round($subtotal + $shipping + $tax, 2),
        ];
    }

    private function shippingRate(float $subtotal): float
    {
        return $subtotal >= 999 ? 0.0 : 99.0; // free shipping above ₹999
    }

    private function taxRate(float $subtotal): float
    {
        return round($subtotal * 0.18, 2); // 18% GST
    }
}