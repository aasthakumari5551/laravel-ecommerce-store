<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    // ── Overview KPIs ────────────────────────────────────────

    /**
     * High-level stats for the dashboard header cards.
     */
    public function overview(int $days = 30): array
    {
        return cache()->remember(
            "analytics:overview:{$days}",
            now()->addMinutes(15),

            fn () => $this->computeOverview($days)
        );
    }

    private function computeOverview(int $days = 30): array
    {
        $from = now()->subDays($days)->startOfDay();

        $current = $this->periodStats(
            $from,
            now()
        );

        $prior = $this->periodStats(
            now()->subDays($days * 2)->startOfDay(),
            $from,
        );

        return [
            'revenue'        => $current['revenue'],
            'orders'         => $current['orders'],
            'aov'            => $current['aov'],
            'customers'      => $current['customers'],

            'revenue_delta'  => $this->delta(
                $prior['revenue'],
                $current['revenue']
            ),

            'orders_delta'   => $this->delta(
                $prior['orders'],
                $current['orders']
            ),

            'aov_delta'      => $this->delta(
                $prior['aov'],
                $current['aov']
            ),

            'customer_delta' => $this->delta(
                $prior['customers'],
                $current['customers']
            ),
        ];
    }

    // ── Revenue chart ─────────────────────────────────────────

    /**
     * Daily revenue for the last N days — feeds line chart.
     * Returns ['labels' => [...], 'data' => [...]]
     */
    public function revenueByDay(int $days = 30): array
    {
        $rows = Order::where(
                        'payment_status',
                        PaymentStatus::Paid->value
                    )
                    ->where(
                        'paid_at',
                        '>=',
                        now()->subDays($days)->startOfDay()
                    )
                    ->selectRaw('
                        DATE(paid_at) as date,
                        SUM(total) as revenue,
                        COUNT(*) as orders
                    ')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->keyBy('date');

        $labels  = [];
        $revenue = [];
        $orders  = [];

        for ($i = $days - 1; $i >= 0; $i--) {

            $date = now()
                ->subDays($i)
                ->toDateString();

            $labels[] = now()
                ->subDays($i)
                ->format('d M');

            $revenue[] = (float) (
                $rows[$date]->revenue ?? 0
            );

            $orders[] = (int) (
                $rows[$date]->orders ?? 0
            );
        }

        return compact(
            'labels',
            'revenue',
            'orders'
        );
    }

    // ── Top products ──────────────────────────────────────────

    /**
     * Best-selling products by revenue and units sold.
     */
    public function topProducts(
        int $limit = 10,
        int $days = 30
    ): \Illuminate\Support\Collection {

        return DB::table('order_items')
                 ->join(
                     'orders',
                     'orders.id',
                     '=',
                     'order_items.order_id'
                 )
                 ->join(
                     'products',
                     'products.id',
                     '=',
                     'order_items.product_id'
                 )
                 ->where(
                     'orders.payment_status',
                     PaymentStatus::Paid->value
                 )
                 ->where(
                     'orders.paid_at',
                     '>=',
                     now()->subDays($days)
                 )
                 ->selectRaw('
                     products.id,
                     products.name,
                     products.slug,
                     products.price,
                     products.stock,
                     SUM(order_items.quantity) as units_sold,
                     SUM(order_items.subtotal) as revenue
                 ')
                 ->groupBy(
                     'products.id',
                     'products.name',
                     'products.slug',
                     'products.price',
                     'products.stock'
                 )
                 ->orderByDesc('revenue')
                 ->limit($limit)
                 ->get();
    }

    // ── Top categories ────────────────────────────────────────

    public function topCategories(
        int $days = 30
    ): \Illuminate\Support\Collection {

        return DB::table('order_items')
                 ->join(
                     'orders',
                     'orders.id',
                     '=',
                     'order_items.order_id'
                 )
                 ->join(
                     'products',
                     'products.id',
                     '=',
                     'order_items.product_id'
                 )
                 ->join(
                     'categories',
                     'categories.id',
                     '=',
                     'products.category_id'
                 )
                 ->where(
                     'orders.payment_status',
                     PaymentStatus::Paid->value
                 )
                 ->where(
                     'orders.paid_at',
                     '>=',
                     now()->subDays($days)
                 )
                 ->selectRaw('
                     categories.id,
                     categories.name,
                     SUM(order_items.subtotal) as revenue,
                     SUM(order_items.quantity) as units_sold
                 ')
                 ->groupBy(
                     'categories.id',
                     'categories.name'
                 )
                 ->orderByDesc('revenue')
                 ->get();
    }

    // ── Order status breakdown ────────────────────────────────

    /**
     * Count of orders by status — feeds pie/donut chart.
     */
    public function orderStatusBreakdown(): array
    {
        $rows = Order::selectRaw(
                    'status, COUNT(*) as count'
                )
                ->groupBy('status')
                ->pluck('count', 'status');

        return collect(OrderStatus::cases())
            ->map(fn ($s) => [
                'status' => $s->label(),
                'count'  => (int) (
                    $rows[$s->value] ?? 0
                ),
                'color'  => $s->color(),
            ])
            ->all();
    }

    // ── Low-stock alert ───────────────────────────────────────

    public function lowStockProducts(
        int $limit = 15
    ): \Illuminate\Support\Collection {

        return Product::where(
                        'track_inventory',
                        true
                    )
                    ->where(
                        'is_active',
                        true
                    )
                    ->whereColumn(
                        'stock',
                        '<=',
                        'low_stock_threshold'
                    )
                    ->orderBy('stock')
                    ->limit($limit)
                    ->get([
                        'id',
                        'name',
                        'sku',
                        'stock',
                        'low_stock_threshold'
                    ]);
    }

    // ── New customers ─────────────────────────────────────────

    public function newCustomersByDay(
        int $days = 30
    ): array {

        $rows = User::role('customer')
                    ->where(
                        'created_at',
                        '>=',
                        now()->subDays($days)
                    )
                    ->selectRaw(
                        'DATE(created_at) as date, COUNT(*) as count'
                    )
                    ->groupBy('date')
                    ->pluck('count', 'date');

        $labels = [];
        $data   = [];

        for ($i = $days - 1; $i >= 0; $i--) {

            $date = now()
                ->subDays($i)
                ->toDateString();

            $labels[] = now()
                ->subDays($i)
                ->format('d M');

            $data[] = (int) (
                $rows[$date] ?? 0
            );
        }

        return compact('labels', 'data');
    }

    // ── Coupon performance ────────────────────────────────────

    public function couponPerformance(
        int $limit = 10
    ): \Illuminate\Support\Collection {

        return DB::table('coupon_usages')
                 ->join(
                     'coupons',
                     'coupons.id',
                     '=',
                     'coupon_usages.coupon_id'
                 )
                 ->selectRaw('
                     coupons.code,
                     coupons.discount_type,
                     COUNT(coupon_usages.id) as total_uses,
                     SUM(coupon_usages.discount_applied) as total_discount
                 ')
                 ->groupBy(
                     'coupons.id',
                     'coupons.code',
                     'coupons.discount_type'
                 )
                 ->orderByDesc('total_uses')
                 ->limit($limit)
                 ->get();
    }

    // ── Private helpers ───────────────────────────────────────

    private function periodStats(
        Carbon $from,
        Carbon $to
    ): array {

        $result = Order::where(
                        'payment_status',
                        PaymentStatus::Paid->value
                    )
                    ->whereBetween(
                        'paid_at',
                        [$from, $to]
                    )
                    ->selectRaw('
                        COUNT(DISTINCT id) as orders,
                        SUM(total) as revenue,
                        COUNT(DISTINCT user_id) as customers
                    ')
                    ->first();

        $revenue = (float) (
            $result->revenue ?? 0
        );

        $orders = (int) (
            $result->orders ?? 0
        );

        return [
            'revenue'   => $revenue,

            'orders'    => $orders,

            'aov'       => $orders > 0
                ? round($revenue / $orders, 2)
                : 0,

            'customers' => (int) (
                $result->customers ?? 0
            ),
        ];
    }

    /**
     * Returns signed percentage change.
     */
    private function delta(
        float $previous,
        float $current
    ): float {

        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round(
            (($current - $previous) / $previous) * 100,
            1
        );
    }
}