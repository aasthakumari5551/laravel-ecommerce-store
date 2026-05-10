@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Orders</h1>
    </div>

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Order number or email…"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 w-64">

        <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Statuses</option>
            @foreach ($orderStatuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>

        <select name="payment_status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
            <option value="">All Payments</option>
            @foreach ($payStatuses as $status)
                <option value="{{ $status->value }}" @selected(request('payment_status') === $status->value)>
                    {{ $status->label() }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            Filter
        </button>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 self-center hover:underline">
            Clear
        </a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Order</th>
                    <th class="px-6 py-3 text-left">Customer</th>
                    <th class="px-6 py-3 text-left">Items</th>
                    <th class="px-6 py-3 text-right">Total</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Payment</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-mono font-medium text-gray-900">{{ $order->number }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $order->user->email }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $order->items->count() }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-gray-900">
                            ₹{{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs {{ $order->isPaid() ? 'text-green-600 font-semibold' : 'text-gray-400' }}">
                                {{ $order->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="text-indigo-600 hover:underline text-xs font-medium">
                                View →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection