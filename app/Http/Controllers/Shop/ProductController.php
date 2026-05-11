<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProductFilterRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\CacheService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private SearchService $searchService,
        private CacheService  $cacheService,
    ) {}

    public function index(ProductFilterRequest $request)
    {
        $products   = $this->searchService->products($request->filters());
        $categories = $this->cacheService->categories();
        $priceRange = $this->searchService->priceRange($request->category);

        return view('shop.products.index', compact('products', 'categories', 'priceRange'));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->load(['category', 'images', 'approvedReviews.user']);

        $related = $this->cacheService->relatedProducts($product);

        return view('shop.products.show', compact('product', 'related'));
    }

    public function suggestions(ProductFilterRequest $request): JsonResponse
    {
        $results = $this->searchService->suggestions($request->input('q', ''));
        return response()->json($results);
    }
}