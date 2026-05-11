<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analytics) {}

    public function dashboard(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 30, 90]) ? $days : 30;

        $overview   = $this->analytics->overview($days);
        $revenue    = $this->analytics->revenueByDay($days);
        $products   = $this->analytics->topProducts(8, $days);
        $categories = $this->analytics->topCategories($days);
        $statuses   = $this->analytics->orderStatusBreakdown();
        $lowStock   = $this->analytics->lowStockProducts();
        $customers  = $this->analytics->newCustomersByDay($days);
        $coupons    = $this->analytics->couponPerformance(5);

        return view('admin.analytics.dashboard', compact(
            'days', 'overview', 'revenue', 'products',
            'categories', 'statuses', 'lowStock', 'customers', 'coupons',
        ));
    }
}