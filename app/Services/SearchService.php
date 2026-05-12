<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchService
{
    private const PER_PAGE     = 16;
    private const SORT_OPTIONS = ['price_asc','price_desc','newest','rating','popular'];

    public function products(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['primaryImage', 'category'])
            ->withCount(['reviews as approved_review_count' => fn ($q) => $q->approved()]);

        // ── Full-text search across name, brand, short_description, tags ──
        if (! empty($filters['q'])) {
            $raw = trim($filters['q']);

            // Split into individual tokens for broader matching
            $tokens = array_filter(explode(' ', $raw));

            $query->where(function ($q) use ($raw, $tokens) {
                // Exact phrase in name (highest relevance — handled via orderByRaw below)
                $q->where('name', 'like', "%{$raw}%")
                  ->orWhere('brand', 'like', "%{$raw}%")
                  ->orWhere('short_description', 'like', "%{$raw}%")
                  ->orWhere('sku', 'like', "%{$raw}%")
                  ->orWhereHas('category', fn ($cq) =>
                        $cq->where('name', 'like', "%{$raw}%")
                  );

                // Token-level matching for multi-word queries
                foreach ($tokens as $token) {
                    if (strlen($token) >= 3) {
                        $q->orWhere('name',              'like', "%{$token}%")
                          ->orWhere('brand',             'like', "%{$token}%")
                          ->orWhere('short_description', 'like', "%{$token}%");
                    }
                }
            });

            // Boost: exact name matches first
            $query->orderByRaw(
                "CASE WHEN name LIKE ? THEN 0
                       WHEN brand LIKE ? THEN 1
                       ELSE 2 END",
                ["%{$raw}%", "%{$raw}%"]
            );
        }

        // ── Category (slug or id) ──
        if (! empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category'])
                  ->orWhere('id',   $filters['category']);
            });
        }

        // ── Brand ──
        if (! empty($filters['brand'])) {
            $query->where('brand', 'like', "%{$filters['brand']}%");
        }

        // ── Tag ──
        if (! empty($filters['tag'])) {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        // ── Price range ──
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // ── Rating ──
        if (! empty($filters['min_rating']) && is_numeric($filters['min_rating'])) {
            $query->where('avg_rating', '>=', $filters['min_rating']);
        }

        // ── Stock ──
        if (! empty($filters['in_stock'])) {
            $query->inStock();
        }

        // ── Featured ──
        if (! empty($filters['featured'])) {
            $query->featured();
        }

        // ── Sort (only add if no search relevance ordering) ──
        $sort = in_array($filters['sort'] ?? '', self::SORT_OPTIONS)
            ? $filters['sort'] : 'newest';

        if (empty($filters['q'])) {
            match($sort) {
                'price_asc'  => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'rating'     => $query->orderByDesc('avg_rating'),
                'popular'    => $query->orderByDesc('review_count'),
                default      => $query->orderByDesc('created_at'),
            };
        } else {
            // After relevance ORDER BY, secondary sort by rating
            $query->orderByDesc('avg_rating');
        }

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    /**
     * Autocomplete: searches name + brand + category name.
     * Returns max 8 results, cached 10 min per query.
     */
    public function suggestions(string $term): Collection
    {
        if (strlen(trim($term)) < 2) {
            return collect();
        }

        $key = 'search_suggest_' . md5(strtolower(trim($term)));

        return cache()->remember($key, now()->addMinutes(10), function () use ($term) {
            return Product::active()
                ->where(function ($q) use ($term) {
                    $q->where('name',  'like', "%{$term}%")
                      ->orWhere('brand', 'like', "%{$term}%")
                      ->orWhereHas('category', fn ($cq) =>
                            $cq->where('name', 'like', "%{$term}%")
                      );
                })
                ->orderByDesc('review_count')
                ->limit(8)
                ->get(['uuid', 'name', 'brand', 'price', 'slug'])
                ->map(fn ($p) => [
                    'uuid'  => $p->uuid,
                    'name'  => $p->name,
                    'brand' => $p->brand,
                    'price' => $p->price,
                    'slug'  => $p->slug,
                ]);
        });
    }

    /**
     * Trending fallback: shown when search bar is focused but empty.
     */
    public function trending(int $limit = 6): Collection
    {
        return cache()->remember('search_trending', now()->addMinutes(30), function () use ($limit) {
            return Product::active()
                ->inStock()
                ->whereJsonContains('tags', 'trending')
                ->orderByDesc('review_count')
                ->limit($limit)
                ->get(['uuid', 'name', 'brand', 'price', 'avg_rating']);
        });
    }

    /**
     * Price range for filter slider UI.
     */
    public function priceRange(?string $categorySlug = null): array
    {
        $q = Product::active();

        if ($categorySlug) {
            $q->whereHas('category', fn ($cq) =>
                $cq->where('slug', $categorySlug)
            );
        }

        $range = $q->selectRaw('MIN(price) as min, MAX(price) as max')->first();

        return [
            'min' => (float) ($range->min ?? 0),
            'max' => (float) ($range->max ?? 200000),
        ];
    }

    /**
     * Distinct brands for filter panel.
     */
    public function brands(?string $categorySlug = null): Collection
    {
        $q = Product::active()->whereNotNull('brand');

        if ($categorySlug) {
            $q->whereHas('category', fn ($cq) =>
                $cq->where('slug', $categorySlug)
            );
        }

        return $q->distinct()
                 ->orderBy('brand')
                 ->pluck('brand');
    }
}