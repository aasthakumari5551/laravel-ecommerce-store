<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModerateReviewRequest;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reviews = $query->paginate(25)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function moderate(ModerateReviewRequest $request, Review $review): RedirectResponse
    {
        match ($request->validated('action')) {
            'approve' => $this->reviewService->approve($review),
            'reject'  => $this->reviewService->reject($review, $request->validated('rejection_reason')),
        };

        return back()->with('success', 'Review ' . $request->action . 'd.');
    }
}