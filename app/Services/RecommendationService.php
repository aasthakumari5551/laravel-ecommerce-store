<?php

namespace App\Services;

use App\Models\Product;
use App\Models\RecentlyViewed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    // ── Recently viewed ───────────────────────────────────

    public function recordView(Product $product): void
{
    $userId    = Auth::id();
    $sessionId = session()->getId();

    if ($userId) {
        // Authenticated: upsert on user_id + product_id
        \DB::table('recently_viewed')->upsert(
            [
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'viewed_at'  => now(),
            ],
            ['user_id', 'product_id'],   // conflict columns
            ['session_id', 'viewed_at'], // update columns
        );
    } else {
        // Guest: delete old + insert (avoids NULL unique issue)
        \DB::table('recently_viewed')
            ->where('session_id', $sessionId)
            ->where('product_id', $product->id)
            ->whereNull('user_id')
            ->delete();

        \DB::table('recently_viewed')->insert([
            'user_id'    => null,
            'session_id' => $sessionId,
            'product_id' => $product->id,
            'viewed_at'  => now(),
        ]);
    }

    $this->pruneOldViews($userId, $sessionId);
}

    public function recentlyViewed(int $limit = 8): Collection
    {
        $userId    = Auth::id();
        $sessionId = session()->getId();

        $query = RecentlyViewed::with(['product.primaryImage', 'product.category'])
            ->orderByDesc('viewed_at')
            ->limit($limit);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId)->whereNull('user_id');
        }

        return $query->get()
            ->pluck('product')
            ->filter(fn ($p) => $p && $p->is_active);
    }

    // ── Customers also bought ─────────────────────────────
    // Find products frequently ordered together with this product

    public function alsoOrdered(Product $product, int $limit = 8): Collection
    {
        $cacheKey = "also_ordered_{$product->id}";

        return cache()->remember($cacheKey, now()->addHours(2), function () use ($product, $limit) {
            // Orders that contain this product
            $orderIds = DB::table('order_items')
                ->where('product_id', $product->id)
                ->pluck('order_id');

            if ($orderIds->isEmpty()) {
                return $this->fallbackRelated($product, $limit);
            }

            // Other products in those orders, ranked by co-occurrence
            $productIds = DB::table('order_items')
                ->whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $product->id)
                ->select('product_id', DB::raw('COUNT(*) as freq'))
                ->groupBy('product_id')
                ->orderByDesc('freq')
                ->limit($limit)
                ->pluck('product_id');

            if ($productIds->isEmpty()) {
                return $this->fallbackRelated($product, $limit);
            }

            return Product::active()
                ->inStock()
                ->whereIn('id', $productIds)
                ->with(['primaryImage'])
                ->get()
                ->sortBy(fn ($p) => $productIds->search($p->id));
        });
    }

    // ── Trending ──────────────────────────────────────────

    public function trending(int $limit = 10): Collection
    {
        return cache()->remember('rec_trending', now()->addMinutes(30), function () use ($limit) {
            return Product::active()
                ->inStock()
                ->whereJsonContains('tags', 'trending')
                ->with(['primaryImage', 'category'])
                ->orderByDesc('review_count')
                ->limit($limit)
                ->get();
        });
    }

    // ── Flash sale ────────────────────────────────────────

    public function flashSale(int $limit = 8): Collection
    {
        return cache()->remember('rec_flash_sale', now()->addMinutes(15), function () use ($limit) {
            return Product::active()
                ->inStock()
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'price')
                ->with(['primaryImage', 'category'])
                ->orderByDesc(DB::raw('(compare_price - price) / compare_price'))
                ->limit($limit)
                ->get();
        });
    }

    // ── Popular brands ────────────────────────────────────

    public function popularBrands(int $limit = 10): Collection
    {
        return cache()->remember('rec_brands', now()->addHour(), function () use ($limit) {
            return Product::active()
                ->whereNotNull('brand')
                ->select('brand', DB::raw('COUNT(*) as product_count'),
                                  DB::raw('AVG(avg_rating) as avg_rating'),
                                  DB::raw('SUM(review_count) as total_reviews'))
                ->groupBy('brand')
                ->orderByDesc('total_reviews')
                ->limit($limit)
                ->get();
        });
    }

    // ── Featured collections ──────────────────────────────

    public function featuredCollections(): array
    {
        return [
            [
                'title'  => 'New Arrivals',
                'tag'    => 'new',
                'sort'   => 'newest',
                'color'  => 'from-blue-600 to-indigo-700',
                'icon'   => '✨',
            ],
            [
                'title'  => 'Best Sellers',
                'tag'    => 'bestseller',
                'sort'   => 'popular',
                'color'  => 'from-brand-600 to-orange-600',
                'icon'   => '🔥',
            ],
            [
                'title'  => 'Premium Picks',
                'tag'    => 'premium',
                'sort'   => 'rating',
                'color'  => 'from-purple-600 to-pink-600',
                'icon'   => '👑',
            ],
            [
                'title'  => 'Under ₹999',
                'tag'    => null,
                'sort'   => 'price_asc',
                'color'  => 'from-green-600 to-teal-600',
                'icon'   => '💚',
                'max_price' => 999,
            ],
        ];
    }

    // ── Delivery estimate ─────────────────────────────────

    public function deliveryEstimate(): array
    {
        $now   = now();
        $hour  = (int) $now->format('H');

        // Orders placed before 3pm get same-day dispatch
        $cutoff      = 15;
        $sameDay     = $hour < $cutoff;
        $dispatchDay = $sameDay ? 'today' : 'tomorrow';

        $delivery = $now->copy()->addDays($sameDay ? 2 : 3);
        // Skip Sundays
        if ($delivery->dayOfWeek === 0) {
            $delivery->addDay();
        }

        $express = $now->copy()->addDay();
        if ($express->dayOfWeek === 0) {
            $express->addDay();
        }

        return [
            'standard'     => $delivery->format('D, d M'),
            'express'       => $express->format('D, d M'),
            'dispatch_day'  => $dispatchDay,
            'cutoff_hour'   => $cutoff,
            'order_by'      => $sameDay
                ? 'Order within ' . (($cutoff - $hour)) . ' hrs for same-day dispatch'
                : 'Order before ' . $cutoff . ':00 tomorrow for next-day dispatch',
            'is_free_standard' => true,
        ];
    }

    // ── Private ───────────────────────────────────────────

    private function fallbackRelated(Product $product, int $limit): Collection
    {
        return Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['primaryImage'])
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();
    }

    private function pruneOldViews(?int $userId, string $sessionId): void
    {
        if ($userId) {
            $keep = RecentlyViewed::where('user_id', $userId)
                ->orderByDesc('viewed_at')->limit(20)->pluck('id');

            RecentlyViewed::where('user_id', $userId)
                ->whereNotIn('id', $keep)->delete();
        } else {
            $keep = RecentlyViewed::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->orderByDesc('viewed_at')->limit(20)->pluck('id');

            RecentlyViewed::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->whereNotIn('id', $keep)->delete();
        }
    }
}