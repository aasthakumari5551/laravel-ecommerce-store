@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Header + period toggle --}}
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Analytics</h1>
        <div class="flex gap-2">
            @foreach ([7, 30, 90] as $d)
                <a href="{{ route('admin.analytics.dashboard', ['days' => $d]) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition
                          {{ $days === $d
                              ? 'bg-indigo-600 text-white'
                              : 'bg-white border border-gray-200 text-gray-600 hover:border-indigo-300' }}">
                    {{ $d }}d
                </a>
            @endforeach
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
            $kpis = [
                ['label' => 'Revenue',     'value' => '₹'.number_format($overview['revenue'],2),  'delta' => $overview['revenue_delta']],
                ['label' => 'Orders',      'value' => $overview['orders'],                         'delta' => $overview['orders_delta']],
                ['label' => 'Avg Order',   'value' => '₹'.number_format($overview['aov'],2),       'delta' => $overview['aov_delta']],
                ['label' => 'Customers',   'value' => $overview['customers'],                      'delta' => $overview['customer_delta']],
            ];
        @endphp

        @foreach ($kpis as $kpi)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">{{ $kpi['label'] }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ $kpi['value'] }}</p>
                @php $positive = $kpi['delta'] >= 0; @endphp
                <p class="text-xs mt-1 {{ $positive ? 'text-green-600' : 'text-red-500' }}">
                    {{ $positive ? '↑' : '↓' }} {{ abs($kpi['delta']) }}% vs prev period
                </p>
            </div>
        @endforeach
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5 mb-6">
        <h2 class="font-semibold text-gray-900 mb-4">Revenue — Last {{ $days }} Days</h2>
        <canvas id="revenueChart" height="80"></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Top Products --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-900">
                Top Products
            </div>
            <div class="divide-y divide-gray-50">
                @foreach ($products as $product)
                    <div class="flex items-center justify-between px-6 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($product->units_sold) }} units</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">
                            ₹{{ number_format($product->revenue, 0) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order Status Breakdown --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
            <h2 class="font-semibold text-gray-900 mb-4">Orders by Status</h2>
            @php $total = collect($statuses)->sum('count'); @endphp
            <div class="space-y-3">
                @foreach ($statuses as $s)
                    @if ($s['count'] > 0)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700">{{ $s['status'] }}</span>
                                <span class="font-semibold text-gray-900">{{ $s['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="h-2 rounded-full bg-{{ $s['color'] }}-500"
                                     style="width: {{ $total > 0 ? round($s['count']/$total*100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Low Stock Alert --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Low Stock</h2>
                <span class="text-xs text-red-500 font-semibold">
                    {{ $lowStock->count() }} product(s)
                </span>
            </div>
            @forelse ($lowStock as $product)
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                        @if ($product->sku)
                            <p class="text-xs font-mono text-gray-400">{{ $product->sku }}</p>
                        @endif
                    </div>
                    <span class="text-sm font-bold {{ $product->stock === 0 ? 'text-red-600' : 'text-orange-500' }}">
                        {{ $product->stock }} left
                    </span>
                </div>
            @empty
                <p class="px-6 py-4 text-sm text-gray-400">All products are well-stocked.</p>
            @endforelse
        </div>

        {{-- Coupon Performance --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-900">
                Top Coupons
            </div>
            @forelse ($coupons as $coupon)
                <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-mono font-semibold text-gray-900">{{ $coupon->code }}</p>
                        <p class="text-xs text-gray-400">{{ $coupon->total_uses }} uses</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">
                        ₹{{ number_format($coupon->total_discount, 0) }} saved
                    </p>
                </div>
            @empty
                <p class="px-6 py-4 text-sm text-gray-400">No coupon data yet.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($revenue['labels']),
        datasets: [{
            label: 'Revenue (₹)',
            data: @json($revenue['revenue']),
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointRadius: 3,
            pointBackgroundColor: '#6366f1',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => '₹' + v.toLocaleString('en-IN'),
                    font: { size: 11 },
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { size: 11 } }
            }
        }
    }
});
</script>
@endsection