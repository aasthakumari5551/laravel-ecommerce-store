<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CacheService;
use App\Services\RecommendationService;

class HomeController extends Controller
{
    public function __construct(
        private CacheService $cache,
        private RecommendationService $rec,
    ) {}

    public function index()
    {
        $categories = $this->cache->categories();

        $featuredProducts = $this->cache
            ->featuredProducts();

        $trending = $this->rec
            ->trending(10);

        $flashSale = $this->rec
            ->flashSale(8);

        $popularBrands = $this->rec
            ->popularBrands(8);

        $featuredCollections = $this->rec
            ->featuredCollections();

        // Latest products

        $latestProducts = Product::active()
            ->inStock()
            ->with([
                'primaryImage',
                'category',
            ])
            ->latest()
            ->limit(8)
            ->get();

        // Top-rated products

        $topRated = Product::active()
            ->inStock()
            ->with(['primaryImage'])
            ->where('review_count', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit(4)
            ->get();

        return view('home', compact(
            'categories',
            'featuredProducts',
            'trending',
            'flashSale',
            'popularBrands',
            'featuredCollections',
            'latestProducts',
            'topRated',
        ));
    }
}