<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProductFilterRequest;
use App\Http\Resources\ProductResource;
use App\Services\SearchService;

class ProductController extends Controller
{
    public function __construct(private SearchService $searchService) {}

    public function index(ProductFilterRequest $request)
    {
        $products = $this->searchService->products($request->filters());

        return ProductResource::collection($products);
    }

    public function show(string $uuid)
    {
        $product = \App\Models\Product::where('uuid', $uuid)
                       ->active()
                       ->with(['category', 'images', 'approvedReviews'])
                       ->firstOrFail();

        return new ProductResource($product);
    }
}