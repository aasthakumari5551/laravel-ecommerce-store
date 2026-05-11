<?php

namespace App\Http\Controllers;

use App\Http\Requests\Review\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService)
    {
        $this->middleware('auth');
    }

    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        try {
            $this->reviewService->submit($product, $request->validated());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['review' => $e->getMessage()]);
        }

        return back()->with('success', 'Review submitted — it will appear after moderation.');
    }

    public function update(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);
        $this->reviewService->update($review, $request->validated());
        return back()->with('success', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);
        $this->reviewService->delete($review);
        return back()->with('success', 'Review deleted.');
    }
}