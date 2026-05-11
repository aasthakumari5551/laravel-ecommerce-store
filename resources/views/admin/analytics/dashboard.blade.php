@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

{{-- Period toggle --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-lg font-bold text-ink-900">Overview</h2>
        <p class="text-sm text-ink-400">Last {{ $days }} days</p>
    </div>
    <div class="flex gap-1.5 bg-ink-100 rounded-xl p-1">
        @foreach([7, 30, 90] as $d)
            <a href="{{ route('admin.analytics.dashboard', ['days' => $d]) }}"
               class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all
                      {{ $days === $d
                          ? 'bg-white text-ink-900 shadow-sm'
                          : 'text-ink-500 hover:text-ink-700' }}">
                {{ $d }}d
            </a>
        @endforeach
    </div>
</div>

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php $kpis = [
        ['label' => 'Revenue',    'value' => '₹'.number_format($overview['revenue'],0),   'delta' => $overview['revenue_delta'],  'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'brand'],
        ['label' => 'Orders',     'value' => number_format($overview['orders']),           'delta' => $overview['orders_delta'],   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'blue'],
        ['label' => 'Avg Order',  'value' => '₹'.number_format($overview['aov'],0),        'delta' => $overview['aov_delta'],      'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'purple'],
        ['label' => 'Customers',  'value' => number_format($overview['customers']),        'delta' => $overview['customer_delta'], 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'green'],
    ]; @endphp

    @foreach($kpis as $kpi)
        @php $positive = $kpi['delta'] >= 0; @endphp
        <div class="bg-white rounded-xl border border-ink-100 shadow-card p-5">
            <div class="flex items-start justify-between mb-3">
                <div class="w-9 h-9 rounded-lg bg-{{ $kpi['color'] === 'brand' ? 'brand' : $kpi['color'] }}-50
                             flex items-center justify-center">
                    <svg class="w-4 h-4 text-{{ $kpi['color'] === 'brand' ? 'brand-600' : $kpi['color'].'-600' }}"
                         fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                             {{ $positive ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                    {{ $positive ? '↑' : '↓' }}{{ abs($kpi['delta']) }}%
                </span>
            </div>
            <p class="text-2xl font-bold text-ink-900 leading-none mb-1">{{ $kpi['value'] }}</p>
            <p class="text-xs text-ink-400">{{ $kpi['label'] }}</p>
        </div>
    @endforeach
</div>

{{-- Revenue Chart --}}
<div class="bg-white rounded-xl border border-ink-100 shadow-card p-5 sm:p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-ink-900">Revenue Trend</h3>
        <span class="text-xs text-ink-400">₹{{ number_format($overview['revenue'], 0) }} total</span>
    </div>
    <canvas id="revenueChart" height="70"></canvas>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Top Products --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-ink-100 shadow-card">
        <div class="px-5 py-4 border-b border-ink-100 flex items-center justify-between">
            <h3 class="font-semibold text-ink-900 text-sm">Top Products</h3>
            <a href="{{ route('admin.products.index') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium">View all →</a>
        </div>
        <div class="divide-y divide-ink-50">
            @forelse($products->take(6) as $i => $product)
                <div class="flex items-center gap-4 px-5 py-3.5">
                    <span class="text-xs font-bold text-ink-300 w-5 flex-shrink-0">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink-900 truncate">{{ $product->name }}</p>
                        <div class="flex items-center gap-3 mt-0.5">
                            <span class="text-xs text-ink-400">{{ number_format($product->units_sold) }} units</span>
                            @php
                                $maxRevenue = $products->max('revenue');
                                $pct = $maxRevenue > 0 ? ($product->revenue / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="flex-1 bg-ink-100 rounded-full h-1.5 max-w-[100px]">
                                <div class="bg-brand-500 h-1.5 rounded-full"
                                     style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm font-bold text-ink-900 flex-shrink-0">
                        ₹{{ number_format($product->revenue, 0) }}
                    </p>
                </div>
            @empty
                <p class="px-5 py-6 text-sm text-ink-400">No sales data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Order Status Breakdown --}}
    <div class="bg-white rounded-xl border border-ink-100 shadow-card p-5">
        <h3 class="font-semibold text-ink-900 text-sm mb-4">Orders by Status</h3>
        @php $total = collect($statuses)->sum('count'); @endphp
        <div class="space-y-3">
            @foreach($statuses as $s)
                @if($s['count'] > 0)
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-{{ $s['color'] }}-500 flex-shrink-0"></span>
                                <span class="text-xs text-ink-700">{{ $s['status'] }}</span>
                            </div>
                            <span class="text-xs font-bold text-ink-900">{{ $s['count'] }}</span>
                        </div>
                        <div class="w-full bg-ink-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full bg-{{ $s['color'] }}-400"
                                 style="width:{{ $total > 0 ? round($s['count']/$total*100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <a href="{{ route('admin.orders.index') }}"
           class="mt-5 block text-center text-xs font-medium text-brand-600 hover:text-brand-700
                  bg-brand-50 hover:bg-brand-100 rounded-lg py-2 transition-colors">
            View All Orders →
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Low Stock Alert --}}
    <div class="bg-white rounded-xl border border-ink-100 shadow-card">
        <div class="px-5 py-4 border-b border-ink-100 flex items-center justify-between">
            <h3 class="font-semibold text-ink-900 text-sm flex items-center gap-2">
                <span class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></span>
                Low Stock Alert
            </h3>
            <span class="badge bg-red-50 text-red-600 text-xs">
                {{ $lowStock->count() }} items
            </span>
        </div>
        @forelse($lowStock as $product)
            <div class="flex items-center justify-between px-5 py-3 border-b
                        border-ink-50 last:border-0 hover:bg-ink-50 transition-colors">
                <div>
                    <p class="text-sm font-medium text-ink-900">{{ $product->name }}</p>
                    @if($product->sku)
                        <p class="text-xs font-mono text-ink-400">{{ $product->sku }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold {{ $product->stock === 0 ? 'text-red-600' : 'text-orange-500' }}">
                        {{ $product->stock }} left
                    </span>
                    <a href="{{ route('admin.products.edit', $product) }}"
                       class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                        Edit →
                    </a>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="text-sm text-green-600 font-medium">✓ All products well-stocked</p>
            </div>
        @endforelse
    </div>

    {{-- Coupon Performance --}}
    <div class="bg-white rounded-xl border border-ink-100 shadow-card">
        <div class="px-5 py-4 border-b border-ink-100 flex items-center justify-between">
            <h3 class="font-semibold text-ink-900 text-sm">Top Coupons</h3>
            <a href="{{ route('admin.coupons.index') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium">Manage →</a>
        </div>
        @forelse($coupons as $coupon)
            <div class="flex items-center justify-between px-5 py-3.5
                        border-b border-ink-50 last:border-0">
                <div>
                    <p class="text-sm font-mono font-bold text-ink-900">{{ $coupon->code }}</p>
                    <p class="text-xs text-ink-400 mt-0.5">{{ $coupon->total_uses }} uses</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-ink-900">
                        ₹{{ number_format($coupon->total_discount, 0) }}
                    </p>
                    <p class="text-xs text-ink-400">saved</p>
                </div>
            </div>
        @empty
            <p class="px-5 py-6 text-sm text-ink-400">No coupon usage yet.</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('revenueChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: @json($revenue['labels']),
        datasets: [
            {
                label: 'Revenue',
                data: @json($revenue['revenue']),
                borderColor: '#d97706',
                backgroundColor: 'rgba(217,119,6,0.07)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointBackgroundColor: '#d97706',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
            {
                label: 'Orders',
                data: @json($revenue['orders']),
                borderColor: '#c5bba8',
                backgroundColor: 'transparent',
                borderWidth: 1.5,
                tension: 0.4,
                borderDash: [4,3],
                pointRadius: 2,
                yAxisID: 'y2',
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: { boxWidth: 12, font: { size: 11 }, color: '#6b5c48' }
            },
            tooltip: {
                backgroundColor: '#1a1612',
                titleColor: '#f0ede6',
                bodyColor: '#c5bba8',
                padding: 10,
                callbacks: {
                    label: ctx => ctx.dataset.label === 'Revenue'
                        ? ' ₹' + ctx.parsed.y.toLocaleString('en-IN')
                        : ' ' + ctx.parsed.y + ' orders'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => '₹' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v),
                    font: { size: 11 }, color: '#8c7a62'
                }
            },
            y2: {
                position: 'right',
                beginAtZero: true,
                grid: { display: false },
                ticks: { font: { size: 10 }, color: '#c5bba8' }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 }, color: '#8c7a62' }
            }
        }
    }
});
</script>
@endpush
@endsection