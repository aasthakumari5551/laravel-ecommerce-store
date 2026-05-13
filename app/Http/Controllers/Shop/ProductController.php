<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ProductFilterRequest;
use App\Models\Product;
use App\Services\CacheService;
use App\Services\RecommendationService;
use App\Services\SearchService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private SearchService $searchService,
        private CacheService $cacheService,
        private RecommendationService $rec,
    ) {}

    public function index(ProductFilterRequest $request)
    {
        $filters = $request->filters();

        $products = $this->searchService
            ->products($filters);

        $categories = $this->cacheService
            ->categories();

        $priceRange = $this->searchService
            ->priceRange($request->category);

        $brands = $this->searchService
            ->brands($request->category);

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

        // Record viewed product

        $this->rec->recordView($product);

        $product->load([
            'category',
            'images',
            'approvedReviews.user',
        ]);

        // Recommendations

        $alsoOrdered = $this->rec
            ->alsoOrdered($product, 6);

        $related = $this->cacheService
            ->relatedProducts($product);

        $recentlyViewed = $this->rec
            ->recentlyViewed(6)
            ->filter(fn ($p) => $p->id !== $product->id)
            ->take(5);

        $delivery = $this->rec
            ->deliveryEstimate();

        return view('shop.products.show', compact(
            'product',
            'related',
            'alsoOrdered',
            'recentlyViewed',
            'delivery',
        ));
    }

    /**
     * Search suggestions
     */
    public function suggestions(
        ProductFilterRequest $request
    ): JsonResponse {

        $q = trim($request->input('q', ''));

        $results = strlen($q) >= 2

            ? $this->searchService
                ->suggestions($q)

            : $this->rec
                ->trending(6)
                ->map(fn ($p) => [
                    'uuid'  => $p->uuid,
                    'name'  => $p->name,
                    'brand' => $p->brand,
                    'price' => $p->price,
                ]);

        return response()->json($results);
    }
}