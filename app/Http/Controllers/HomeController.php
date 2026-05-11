<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AnalyticsService;
use App\Services\CacheService;

class HomeController extends Controller
{
    public function __construct(
        private CacheService    $cache,
    ) {}

    public function index()
    {
        $categories      = $this->cache->categories();
        $featuredProducts = $this->cache->featuredProducts();

        // Latest 8 active products
        $latestProducts  = \App\Models\Product::active()
                            ->inStock()
                            ->with(['primaryImage', 'category'])
                            ->latest()
                            ->limit(8)
                            ->get();

        // Top-rated 4
        $topRated        = \App\Models\Product::active()
                            ->inStock()
                            ->with(['primaryImage'])
                            ->where('review_count', '>', 0)
                            ->orderByDesc('avg_rating')
                            ->limit(4)
                            ->get();

        return view('home', compact(
            'categories',
            'featuredProducts',
            'latestProducts',
            'topRated',
        ));
    }
}