<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecentlyViewed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PersonalisationService
{
    /**
     * Build a personalised section title + product list
     * based on browsing history and purchase history.
     */
    public function forYou(int $limit = 10): array
    {
        $userId = Auth::id();

        if (! $userId) {
            return $this->fallback($limit);
        }

        // Categories from recently viewed
        $viewedCategoryIds = RecentlyViewed::where('user_id', $userId)
            ->join('products', 'products.id', '=', 'recently_viewed.product_id')
            ->pluck('products.category_id')
            ->unique()
            ->take(3);

        // Categories from past orders
        $orderedCategoryIds = DB::table('order_items')
            ->join('orders',   'orders.id',   '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.user_id', $userId)
            ->pluck('products.category_id')
            ->unique()
            ->take(3);

        $categoryIds = $viewedCategoryIds
            ->merge($orderedCategoryIds)
            ->unique()
            ->take(4);

        if ($categoryIds->isEmpty()) {
            return $this->fallback($limit);
        }

        $products = Product::active()
            ->inStock()
            ->whereIn('category_id', $categoryIds)
            ->with(['primaryImage', 'category'])
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();

        return [
            'title'    => 'Picked for You',
            'subtitle' => 'Based on your browsing',
            'products' => $products,
        ];
    }

    /**
     * Coupon recommendations: suggest highest-value valid coupon
     * for the current cart subtotal.
     */
    public function recommendCoupon(float $subtotal): ?array
    {
        if (! Auth::check()) return null;

        $userId = Auth::id();

        $coupon = \App\Models\Coupon::active()
            ->where('minimum_order_amount', '<=', $subtotal)
            ->whereDoesntHave('usages', fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('discount_value')
            ->first();

        if (! $coupon) return null;

        return [
            'code'    => $coupon->code,
            'label'   => $coupon->discount_type->value === 'percentage'
                ? "Save {$coupon->discount_value}% with {$coupon->code}"
                : "Save ₹{$coupon->discount_value} with {$coupon->code}",
            'expires' => $coupon->expires_at?->diffForHumans(),
        ];
    }

    private function fallback(int $limit): array
    {
        return [
            'title'    => 'Popular Right Now',
            'subtitle' => 'Everyone\'s buying these',
            'products' => Product::active()->inStock()
                ->with(['primaryImage', 'category'])
                ->whereJsonContains('tags', 'trending')
                ->orderByDesc('review_count')
                ->limit($limit)
                ->get(),
        ];
    }
}