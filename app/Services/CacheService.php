<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const KEY_CATEGORIES = 'app:categories:active';
    private const KEY_FEATURED   = 'app:products:featured';
    private const KEY_PRODUCT    = 'app:product:';
    private const KEY_RELATED    = 'app:related:';
    private const KEY_ANALYTICS  = 'app:analytics:overview';

    private const TTL_LONG   = 3600;
    private const TTL_MEDIUM = 1800;
    private const TTL_SHORT  = 900;

    public function categories(): Collection
    {
        return Cache::remember(
            self::KEY_CATEGORIES, self::TTL_LONG,
            fn () => Category::active()->roots()->ordered()
                        ->withCount(['products' => fn ($q) => $q->active()])
                        ->with(['children' => fn ($q) => $q->active()->ordered()])
                        ->get()
        );
    }

    public function featuredProducts(): Collection
    {
        return Cache::remember(
            self::KEY_FEATURED, self::TTL_MEDIUM,
            fn () => Product::active()->featured()->inStock()
                        ->with(['primaryImage', 'category'])
                        ->ordered()->limit(8)->get()
        );
    }

    public function productByUuid(string $uuid): ?Product
    {
        return Cache::remember(
            self::KEY_PRODUCT . $uuid, self::TTL_MEDIUM,
            fn () => Product::where('uuid', $uuid)
                        ->with(['category', 'images', 'approvedReviews.user'])
                        ->first()
        );
    }

    public function relatedProducts(Product $product): Collection
    {
        return Cache::remember(
            self::KEY_RELATED . $product->id, self::TTL_MEDIUM,
            fn () => Product::active()
                        ->where('category_id', $product->category_id)
                        ->where('id', '!=', $product->id)
                        ->inStock()
                        ->with(['primaryImage'])
                        ->limit(4)->get()
        );
    }

    public function forgetProduct(string $uuid): void
    {
        Cache::forget(self::KEY_PRODUCT . $uuid);
        Cache::forget(self::KEY_FEATURED);
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