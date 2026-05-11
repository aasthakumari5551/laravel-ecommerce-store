<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withTrashed()
                         ->withCount('usages')
                         ->latest()
                         ->paginate(20);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
                         ->with('success', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $coupon->update($data);

        return back()->with('success', 'Coupon updated.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
                         ->with('success', 'Coupon deactivated.');
    }
}