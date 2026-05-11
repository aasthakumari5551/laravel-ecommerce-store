<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    // ── Customer actions ──────────────────────────────────────

    /**
     * Submit a new review. Auto-detects verified purchase.
     *
     * @throws \RuntimeException if user already reviewed this product
     */
    public function submit(Product $product, array $data): Review
    {
        $user = Auth::user();

        $existing = Review::withTrashed()
                          ->where('product_id', $product->id)
                          ->where('user_id', $user->id)
                          ->first();

        if ($existing) {
            throw new \RuntimeException('You have already submitted a review for this product.');
        }

        // Check if user purchased this product (verified purchase badge)
        $verifiedOrder = Order::where('user_id', $user->id)
                              ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                              ->where('payment_status', 'paid')
                              ->first();

        return DB::transaction(function () use ($product, $user, $data, $verifiedOrder) {
            $review = Review::create([
                'product_id'           => $product->id,
                'user_id'              => $user->id,
                'order_id'             => $verifiedOrder?->id,
                'rating'               => $data['rating'],
                'title'                => $data['title'] ?? null,
                'body'                 => $data['body'] ?? null,
                'status'               => ReviewStatus::Pending,
                'is_verified_purchase' => (bool) $verifiedOrder,
            ]);

            // Rating not recalculated yet — happens on approval
            return $review;
        });
    }

    public function update(Review $review, array $data): Review
    {
        $review->update([
            'rating' => $data['rating'],
            'title'  => $data['title'] ?? $review->title,
            'body'   => $data['body']  ?? $review->body,
            'status' => ReviewStatus::Pending, // back to moderation queue on edit
        ]);

        // Recalculate since rating may have changed from previously approved state
        $this->recalculateProductRating($review->product);

        return $review->fresh();
    }

    public function delete(Review $review): void
    {
        DB::transaction(function () use ($review) {
            $product = $review->product;
            $review->delete();
            $this->recalculateProductRating($product);
        });
    }

    // ── Admin moderation ──────────────────────────────────────

    public function approve(Review $review): Review
    {
        return DB::transaction(function () use ($review) {
            $review->update([
                'status'           => ReviewStatus::Approved,
                'rejection_reason' => null,
            ]);
            $this->recalculateProductRating($review->product);
            return $review->fresh();
        });
    }

    public function reject(Review $review, ?string $reason = null): Review
    {
        return DB::transaction(function () use ($review, $reason) {
            $review->update([
                'status'           => ReviewStatus::Rejected,
                'rejection_reason' => $reason,
            ]);
            $this->recalculateProductRating($review->product);
            return $review->fresh();
        });
    }

    // ── Rating recalculation ──────────────────────────────────

    /**
     * Recompute avg_rating and review_count from approved reviews only.
     * Called after any review approve/reject/delete.
     */
    public function recalculateProductRating(Product $product): void
    {
        $stats = Review::where('product_id', $product->id)
                       ->where('status', ReviewStatus::Approved->value)
                       ->selectRaw('COUNT(*) as total, AVG(rating) as average')
                       ->first();

        $product->update([
            'avg_rating'   => round((float) ($stats->average ?? 0), 2),
            'review_count' => (int) ($stats->total ?? 0),
        ]);
    }
}