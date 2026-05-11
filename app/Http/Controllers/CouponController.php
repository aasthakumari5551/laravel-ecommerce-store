<?php

namespace App\Http\Controllers;

use App\Http\Requests\Coupon\ApplyCouponRequest;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService,
        private CartService   $cartService,
    ) {
        $this->middleware('auth');
    }

    public function apply(ApplyCouponRequest $request): RedirectResponse
    {
        $cart = $this->cartService->summary();

        try {
            $coupon = $this->couponService->validate(
                $request->validated('code'),
                $cart['subtotal'],
            );
            $this->couponService->applyToSession($coupon);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['coupon' => $e->getMessage()]);
        }

        return back()->with('success', "Coupon \"{$coupon->code}\" applied.");
    }

    public function remove(): RedirectResponse
    {
        $this->couponService->removeFromSession();
        return back()->with('success', 'Coupon removed.');
    }
}