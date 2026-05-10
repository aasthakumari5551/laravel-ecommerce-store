<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    // ── Cart Resolution ───────────────────────────────────────

    /**
     * Get or create the cart for the current context
     * (auth user → DB cart, guest → session-keyed DB cart).
     */
    public function resolve(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        $sessionId = session()->get('cart_session_id') ?? $this->initGuestSession();

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    private function initGuestSession(): string
    {
        $id = (string) Str::uuid();
        session()->put('cart_session_id', $id);
        return $id;
    }

    // ── Core Operations ───────────────────────────────────────

    /**
     * Add a product to cart. Merges quantity if already present.
     *
     * @throws \RuntimeException on stock failure
     */
    public function add(int $productId, int $quantity = 1): CartItem
    {
        $product = Product::active()->findOrFail($productId);
        $cart    = $this->resolve();

        $this->validateStock($product, $quantity);

        $existing = $cart->items()->where('product_id', $productId)->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            $this->validateStock($product, $newQty);
            $existing->update(['quantity' => $newQty]);
            return $existing->fresh();
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'unit_price' => $product->price, // lock price at add-time
        ]);
    }

    /**
     * Update quantity of a specific cart item.
     *
     * @throws \RuntimeException on stock failure
     */
    public function update(int $cartItemId, int $quantity): CartItem
    {
        $cart = $this->resolve();
        $item = $cart->items()->findOrFail($cartItemId);

        $this->validateStock($item->product, $quantity);

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    /**
     * Remove a single item from cart.
     */
    public function remove(int $cartItemId): void
    {
        $cart = $this->resolve();
        $cart->items()->where('id', $cartItemId)->delete();
    }

    /**
     * Empty the entire cart.
     */
    public function clear(): void
    {
        $this->resolve()->items()->delete();
    }

    // ── Guest → Auth Cart Merge ───────────────────────────────

    /**
     * Merge guest session cart into the authenticated user's cart.
     * Call this in the Login / Register response.
     */
    public function mergeGuestCartOnLogin(): void
    {
        $sessionId = session()->get('cart_session_id');

        if (! $sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();

        if (! $guestCart || $guestCart->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()
                                 ->where('product_id', $guestItem->product_id)
                                 ->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
            } else {
                $userCart->items()->create([
                    'product_id' => $guestItem->product_id,
                    'quantity'   => $guestItem->quantity,
                    'unit_price' => $guestItem->unit_price,
                ]);
            }
        }

        $guestCart->delete();
        session()->forget('cart_session_id');
    }

    // ── Summary ───────────────────────────────────────────────

    public function summary(): array
    {
        $cart  = $this->resolve()->load('items.product');
        $items = $cart->items;

        return [
            'items'       => $items,
            'subtotal'    => $cart->subtotal(),
            'total_items' => $cart->totalItems(),
            'is_empty'    => $cart->isEmpty(),
        ];
    }

    // ── Stock Validation ──────────────────────────────────────

    private function validateStock(Product $product, int $requestedQty): void
    {
        if (! $product->track_inventory) {
            return;
        }

        if ($product->stock < $requestedQty) {
            throw new \RuntimeException(
                "Only {$product->stock} unit(s) of \"{$product->name}\" available."
            );
        }
    }
}