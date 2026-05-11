<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    // ── Cache key constants — no magic strings anywhere ───────

    private const KEY_CATEGORIES       = 'app:categories:active';
    private const KEY_FEATURED         = 'app:products:featured';
    private const KEY_PRODUCT          = 'app:product:'; // + uuid
    private const KEY_RELATED          = 'app:related:'; // + product id
    private const KEY_ANALYTICS        = 'app:analytics:overview';

    private const TTL_LONG   = 3600;  // 1 hour  — categories
    private const TTL_MEDIUM = 1800;  // 30 min  — products, featured
    private const TTL_SHORT  = 900;   // 15 min  — analytics

    // ── Read-through accessors ────────────────────────────────

    public function categories(): Collection
    {
        return Cache::remember(
            self::KEY_CATEGORIES,
            self::TTL_LONG,
            fn () => Category::active()
                ->roots()
                ->ordered()
                ->withCount('products')
                ->with('children')
                ->get()
        );
    }

    public function featuredProducts(): Collection
    {
        return Cache::remember(
            self::KEY_FEATURED,
            self::TTL_MEDIUM,
            fn () => Product::active()
                ->featured()
                ->inStock()
                ->with(['primaryImage'])
                ->ordered()
                ->limit(8)
                ->get()
        );
    }

    public function productByUuid(string $uuid): ?Product
    {
        return Cache::remember(
            self::KEY_PRODUCT . $uuid,
            self::TTL_MEDIUM,
            fn () => Product::where('uuid', $uuid)
                ->with(['category', 'images', 'approvedReviews.user'])
                ->first()
        );
    }

    public function relatedProducts(Product $product): Collection
    {
        return Cache::remember(
            self::KEY_RELATED . $product->id,
            self::TTL_MEDIUM,
            fn () => Product::active()
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->with(['primaryImage'])
                ->inStock()
                ->limit(4)
                ->get()
        );
    }

    // ── Invalidation ──────────────────────────────────────────

    public function forgetProduct(string $uuid): void
    {
        Cache::forget(self::KEY_PRODUCT . $uuid);
        Cache::forget(self::KEY_FEATURED); // featured list may include this product
    }

    public function forgetCategories(): void
    {
        Cache::forget(self::KEY_CATEGORIES);
    }

    public function forgetAnalytics(): void
    {
        Cache::forget(self::KEY_ANALYTICS);
    }

    public function forgetRelated(int $productId): void
    {
        Cache::forget(self::KEY_RELATED . $productId);
    }
}