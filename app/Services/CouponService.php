<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Enums\DiscountType;
use Illuminate\Support\Facades\Auth;

class CouponService
{
    // ── Validation ────────────────────────────────────────────

    /**
     * Validate a coupon code against the current cart and user.
     * Returns the Coupon model on success.
     *
     * @throws \RuntimeException with user-facing message on failure
     */
    public function validate(string $code, float $subtotal): Coupon
    {
        $user   = Auth::user();
        $coupon = Coupon::where('code', strtoupper(trim($code)))->first();

        if (! $coupon) {
            throw new \RuntimeException('Coupon code is invalid.');
        }

        if (! $coupon->is_active) {
            throw new \RuntimeException('This coupon is no longer active.');
        }

        if ($coupon->isExpired()) {
            throw new \RuntimeException('This coupon has expired.');
        }

        if ($coupon->isExhausted()) {
            throw new \RuntimeException('This coupon has reached its usage limit.');
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw new \RuntimeException('This coupon is not yet active.');
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            throw new \RuntimeException(
                "This coupon requires a minimum order of ₹{$coupon->minimum_order_amount}."
            );
        }

        if ($coupon->usage_limit_per_user > 0) {
            $userUsages = $coupon->usagesByUser($user->id);
            if ($userUsages >= $coupon->usage_limit_per_user) {
                throw new \RuntimeException('You have already used this coupon.');
            }
        }

        return $coupon;
    }

    /**
     * Calculate the full discount breakdown for display.
     */
    public function breakdown(Coupon $coupon, float $subtotal, float $shippingAmount): array
    {
        $discountAmount  = $coupon->calculateDiscount($subtotal);
        $shippingDiscount = $coupon->discount_type === DiscountType::FreeShip
            ? $shippingAmount
            : 0.0;

        return [
            'coupon'           => $coupon,
            'discount_amount'  => $discountAmount,
            'shipping_waived'  => $shippingDiscount,
            'total_saved'      => round($discountAmount + $shippingDiscount, 2),
        ];
    }

    /**
     * Record a coupon usage after a successful order.
     * Increments the global used_count atomically.
     */
    public function recordUsage(Order $order, Coupon $coupon, float $discountApplied): void
    {
        CouponUsage::create([
            'coupon_id'        => $coupon->id,
            'order_id'         => $order->id,
            'user_id'          => $order->user_id,
            'discount_applied' => $discountApplied,
        ]);

        // Atomic increment — safe under concurrent requests
        Coupon::where('id', $coupon->id)->increment('used_count');
    }

    /**
     * Store validated coupon in session for checkout pipeline.
     */
    public function applyToSession(Coupon $coupon): void
    {
        session()->put('coupon_code', $coupon->code);
    }

    public function removeFromSession(): void
    {
        session()->forget('coupon_code');
    }

    public function getFromSession(): ?Coupon
    {
        $code = session('coupon_code');
        return $code ? Coupon::active()->where('code', $code)->first() : null;
    }
}