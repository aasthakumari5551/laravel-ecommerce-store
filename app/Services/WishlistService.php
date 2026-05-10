<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;

class WishlistService
{
    // ── Wishlist Resolution ───────────────────────────────────

    public function resolve(): Wishlist
    {
        return Wishlist::firstOrCreate(['user_id' => Auth::id()]);
    }

    // ── Core Operations ───────────────────────────────────────

    /**
     * Toggle: add if absent, remove if present.
     * Returns true if item was added, false if removed.
     */
    public function toggle(int $productId): bool
    {
        $wishlist = $this->resolve();

        $existing = $wishlist->items()
                             ->where('product_id', $productId)
                             ->first();

        if ($existing) {
            $existing->delete();
            return false; // removed
        }

        $wishlist->items()->create(['product_id' => $productId]);
        return true; // added
    }

    public function add(int $productId): WishlistItem
    {
        $wishlist = $this->resolve();

        return $wishlist->items()->firstOrCreate(['product_id' => $productId]);
    }

    public function remove(int $productId): void
    {
        $this->resolve()
             ->items()
             ->where('product_id', $productId)
             ->delete();
    }

    public function contains(int $productId): bool
    {
        return $this->resolve()
                    ->items()
                    ->where('product_id', $productId)
                    ->exists();
    }

    public function all(): Wishlist
    {
        return $this->resolve()->load('products.primaryImage');
    }

    public function clear(): void
    {
        $this->resolve()->items()->delete();
    }
}