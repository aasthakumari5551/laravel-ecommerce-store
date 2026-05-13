<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use App\Services\PersonalisationService;
use App\Services\RecommendationService;
use App\Services\SearchService;

class HomeController extends Controller
{
    public function __construct(
        private CacheService           $cache,
        private RecommendationService  $rec,
        private PersonalisationService $personal,
        private SearchService          $search,
    ) {}

    public function index()
    {
        $categories          = $this->cache->categories();
        $featuredProducts    = $this->cache->featuredProducts();
        $trending            = $this->rec->trending(10);
        $flashSale           = $this->rec->flashSale(8);
        $popularBrands       = $this->rec->popularBrands(8);
        $featuredCollections = $this->rec->featuredCollections();
        $forYou              = $this->personal->forYou(10);
        $trendingKeywords    = $this->search->trendingKeywords(8);

        $latestProducts = \App\Models\Product::active()
            ->inStock()
            ->with(['primaryImage', 'category'])
            ->latest()
            ->limit(8)
            ->get();

        return view('home', compact(
            'categories', 'featuredProducts', 'trending',
            'flashSale', 'popularBrands', 'featuredCollections',
            'forYou', 'trendingKeywords', 'latestProducts',
        ));
    }
}