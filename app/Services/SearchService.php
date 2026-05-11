<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SearchService
{
    private const PER_PAGE      = 16;
    private const SORT_OPTIONS  = ['price_asc', 'price_desc', 'newest', 'rating', 'popular'];

    /**
     * Full filtered + sorted product query.
     * All parameters optional — safe to call with empty array.
     */
    public function products(array $filters): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['primaryImage', 'category']) // eager-load to prevent N+1
            ->withCount(['reviews as approved_review_count' => fn ($q) => $q->approved()]);

        // ── Full-text search ──────────────────────────────────
        if (! empty($filters['q'])) {
            $term = '%' . $filters['q'] . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('short_description', 'like', $term)
                  ->orWhere('sku', 'like', $term);
            });
        }

        // ── Category (supports slug or id) ───────────────────
        if (! empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('slug', $filters['category'])
                  ->orWhere('id', $filters['category']);
            });
        }

        // ── Price range ───────────────────────────────────────
        if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // ── Rating ────────────────────────────────────────────
        if (! empty($filters['min_rating']) && is_numeric($filters['min_rating'])) {
            $query->where('avg_rating', '>=', $filters['min_rating']);
        }

        // ── Stock ─────────────────────────────────────────────
        if (! empty($filters['in_stock'])) {
            $query->inStock();
        }

        // ── Featured ──────────────────────────────────────────
        if (! empty($filters['featured'])) {
            $query->featured();
        }

        // ── Sort ──────────────────────────────────────────────
        $sort = in_array($filters['sort'] ?? '', self::SORT_OPTIONS)
            ? $filters['sort']
            : 'newest';

        match($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating'     => $query->orderByDesc('avg_rating'),
            'popular'    => $query->orderByDesc('review_count'),
            default      => $query->orderByDesc('created_at'), // newest
        };

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    /**
     * Lightweight autocomplete — returns names + uuids only.
     * Max 8 results. Cached per query term.
     */
    public function suggestions(string $term): Collection
    {
        if (strlen($term) < 2) {
            return collect();
        }

        return cache()->remember(
            'search_suggest_' . md5($term),
            now()->addMinutes(10),
            fn () => Product::active()
                ->where('name', 'like', '%' . $term . '%')
                ->limit(8)
                ->get(['uuid', 'name', 'price', 'slug'])
        );
    }

    /**
     * Available price range for filter UI.
     */
    public function priceRange(?string $categorySlug = null): array
    {
        $query = Product::active();
        if ($categorySlug) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $categorySlug));
        }
        $range = $query->selectRaw('MIN(price) as min, MAX(price) as max')->first();

        return [
            'min' => (float) ($range->min ?? 0),
            'max' => (float) ($range->max ?? 10000),
        ];
    }
}