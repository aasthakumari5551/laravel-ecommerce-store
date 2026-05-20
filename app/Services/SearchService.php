<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SearchService
{
    private const PER_PAGE     = 16;
    private const SORT_OPTIONS = ['price_asc','price_desc','newest','rating','popular'];

    // Common typo corrections
    private const CORRECTIONS = [
        'iphon'  => 'iphone',  'samsng'  => 'samsung', 'headfone' => 'headphone',
        'earfone'=> 'earphone','labtop'  => 'laptop',  'mobil'    => 'mobile',
        'tshirt' => 't-shirt', 'jean'    => 'jeans',   'perfum'   => 'perfume',
        'jewlery'=> 'jewelry', 'sandal'  => 'sandals', 'sneker'   => 'sneaker',
        'wotch'  => 'watch',   'sunglas' => 'sunglasses',
    ];

    public function products(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['primaryImage', 'category'])
            ->withCount(['reviews as approved_review_count' => fn ($q) => $q->approved()]);

        if (! empty($filters['q'])) {
            $raw      = trim($filters['q']);
            $corrected = $this->correct($raw);
            $tokens   = array_filter(
                array_unique(array_merge(
                    explode(' ', $raw),
                    explode(' ', $corrected),
                )),
                fn ($t) => strlen($t) >= 2
            );

            $query->where(function ($q) use ($raw, $corrected, $tokens) {
                $q->where('name',              'like', "%{$raw}%")
                  ->orWhere('brand',            'like', "%{$raw}%")
                  ->orWhere('short_description','like', "%{$raw}%")
                  ->orWhere('sku',              'like', "%{$raw}%")
                  ->orWhereHas('category',  fn ($cq) =>
                        $cq->where('name', 'like', "%{$raw}%"));

                if ($corrected !== $raw) {
                    $q->orWhere('name',  'like', "%{$corrected}%")
                      ->orWhere('brand', 'like', "%{$corrected}%");
                }

                foreach ($tokens as $t) {
                    $q->orWhere('name',  'like', "%{$t}%")
                      ->orWhere('brand', 'like', "%{$t}%");
                }
            });

            // Relevance ordering
            $query->orderByRaw(
                'CASE
                    WHEN name  LIKE ? THEN 0
                    WHEN brand LIKE ? THEN 1
                    WHEN name  LIKE ? THEN 2
                    ELSE 3
                 END',
                ["%{$raw}%", "%{$raw}%", "%{$corrected}%"]
            );
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($q) =>
                $q->where('slug', $filters['category'])
                  ->orWhere('id',  $filters['category']));
        }

        if (! empty($filters['brand'])) {
            $query->where('brand', 'like', "%{$filters['brand']}%");
        }

        if (! empty($filters['tag'])) {
            $query->whereJsonContains('tags', $filters['tag']);
        }

        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (! empty($filters['min_rating'])) {
            $query->where('avg_rating', '>=', $filters['min_rating']);
        }

        if (! empty($filters['in_stock'])) {
            $query->inStock();
        }

        if (! empty($filters['featured'])) {
            $query->featured();
        }

        if (empty($filters['q'])) {
            $sort = in_array($filters['sort'] ?? '', self::SORT_OPTIONS)
                ? $filters['sort'] : 'newest';

            match($sort) {
                'price_asc'  => $query->orderBy('price'),
                'price_desc' => $query->orderByDesc('price'),
                'rating'     => $query->orderByDesc('avg_rating'),
                'popular'    => $query->orderByDesc('review_count'),
                default      => $query->orderByDesc('created_at'),
            };
        } else {
            $query->orderByDesc('avg_rating');
        }

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    public function suggestions(string $term): Collection
    {
        if (strlen(trim($term)) < 2) {
            return collect();
        }

        $corrected = $this->correct($term);
        $key       = 'search_suggest_' . md5(strtolower(trim($term)));

        return cache()->remember($key, now()->addMinutes(10), function () use ($term, $corrected) {
            return Product::active()
                ->where(function ($q) use ($term, $corrected) {
                    $q->where('name',  'like', "%{$term}%")
                      ->orWhere('brand','like', "%{$term}%")
                      ->orWhereHas('category', fn ($cq) =>
                            $cq->where('name', 'like', "%{$term}%"));

                    if ($corrected !== $term) {
                        $q->orWhere('name',  'like', "%{$corrected}%")
                          ->orWhere('brand', 'like', "%{$corrected}%");
                    }
                })
                ->orderByDesc('review_count')
                ->limit(8)
                ->get(['uuid','name','brand','price','slug'])
                ->map(fn ($p) => [
                    'uuid'      => $p->uuid,
                    'name'      => $p->name,
                    'brand'     => $p->brand,
                    'price'     => $p->price,
                    'corrected' => $corrected !== $term ? $corrected : null,
                ]);
        });
    }

    /**
     * Trending search terms from order items + tag frequency.
     */
    public function trendingKeywords(int $limit = 8): array
    {
        return cache()->remember('trending_keywords', now()->addHour(), function () use ($limit) {
            $fromOrders = Product::query()
    ->whereNotNull('products.brand')
    ->selectRaw('products.brand, COUNT(*) as freq')
    ->groupBy('products.brand')
    ->orderByDesc('freq')
    ->limit($limit)
    ->pluck('brand')
    ->toArray();

            $fallback = ['Smartphones', 'Running Shoes', 'Skincare', 'Laptops',
                         'Headphones', 'Kurta', 'Watches', 'Yoga Mat'];

            return array_slice(array_unique(array_merge($fromOrders, $fallback)), 0, $limit);
        });
    }

    /**
     * Did-you-mean correction for a raw query string.
     */
    public function didYouMean(string $raw): ?string
    {
        $corrected = $this->correct($raw);
        return $corrected !== $raw ? $corrected : null;
    }

    public function trending(int $limit = 10): Collection
    {
        return cache()->remember('rec_trending', now()->addMinutes(30), function () use ($limit) {
            return Product::active()->inStock()
                ->whereJsonContains('tags', 'trending')
                ->with(['primaryImage', 'category'])
                ->orderByDesc('review_count')
                ->limit($limit)
                ->get();
        });
    }

    public function priceRange(?string $categorySlug = null): array
    {
        $q = Product::active();
        if ($categorySlug) {
            $q->whereHas('category', fn ($cq) => $cq->where('slug', $categorySlug));
        }
        $range = $q->selectRaw('MIN(price) as min, MAX(price) as max')->first();
        return ['min' => (float) ($range->min ?? 0), 'max' => (float) ($range->max ?? 200000)];
    }

    public function brands(?string $categorySlug = null): Collection
    {
        $q = Product::active()->whereNotNull('brand');
        if ($categorySlug) {
            $q->whereHas('category', fn ($cq) => $cq->where('slug', $categorySlug));
        }
        return $q->distinct()->orderBy('brand')->pluck('brand');
    }

    // ── Private ───────────────────────────────────────────

    private function correct(string $term): string
    {
        $words = explode(' ', strtolower($term));
        $fixed = array_map(fn ($w) => self::CORRECTIONS[$w] ?? $w, $words);
        return implode(' ', $fixed);
    }
}