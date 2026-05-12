<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProductFilterRequest;
use App\Services\CacheService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(
        private SearchService $searchService,
        private CacheService $cacheService,
    ) {}

    public function index(ProductFilterRequest $request)
    {
        $filters = $request->filters();

        $products = $this->searchService->products($filters);

        $categories = $this->cacheService->categories();

        $priceRange = $this->searchService->priceRange(
            $request->category
        );

        $brands = $this->searchService->brands(
            $request->category
        );

        return view('shop.products.index', compact(
            'products',
            'categories',
            'priceRange',
            'brands',
        ));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->load([
            'category',
            'images',
            'approvedReviews.user',
        ]);

        $related = $this->cacheService
            ->relatedProducts($product);

        return view('shop.products.show', compact(
            'product',
            'related',
        ));
    }

    /**
     * Autocomplete + trending fallback
     */
    public function suggestions(
        ProductFilterRequest $request
    ): JsonResponse {

        $q = trim($request->input('q', ''));

        if (strlen($q) >= 2) {

            $results = $this->searchService
                ->suggestions($q);

        } else {

            $results = $this->searchService
                ->trending(6);
        }

        return response()->json($results);
    }
}