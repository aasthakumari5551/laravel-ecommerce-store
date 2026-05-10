@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Order {{ $order->number }}</h1>
            <p class="text-sm text-gray-500 mt-1">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="text-right">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                {{ $order->status->label() }}
            </span>
            <p class="text-xs text-gray-400 mt-1">Payment: {{ $order->payment_status->label() }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->has('cancel'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-6 text-sm">
            {{ $errors->first('cancel') }}
        </div>
    @endif

    {{-- Order items --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Items Ordered</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                        @if ($item->product_sku)
                            <p class="text-xs text-gray-400 font-mono mt-0.5">SKU: {{ $item->product_sku }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-0.5">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">₹{{ number_format($item->subtotal, 2) }}</p>
                </div>
            @endforeach
        </div>
        {{-- Totals --}}
        <div class="px-6 py-4 bg-gray-50 rounded-b-xl space-y-1.5">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>Shipping</span>
                <span>{{ $order->shipping_amount == 0 ? 'Free' : '₹' . number_format($order->shipping_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <span>GST (18%)</span><span>₹{{ number_format($order->tax_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-base font-bold text-gray-900 pt-1 border-t border-gray-200">
                <span>Total</span><span>₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Shipping address --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 px-6 py-5">
        <h2 class="font-semibold text-gray-900 mb-3">Delivery Address</h2>
        <div class="text-sm text-gray-600 space-y-0.5">
            <p class="font-medium text-gray-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
            <p>{{ $order->shipping_line1 }}{{ $order->shipping_line2 ? ', ' . $order->shipping_line2 : '' }}</p>
            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}</p>
            <p>{{ $order->shipping_country }}</p>
            <p class="mt-1">📞 {{ $order->shipping_phone }}</p>
        </div>
    </div>

    {{-- Status history --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 px-6 py-5">
        <h2 class="font-semibold text-gray-900 mb-4">Order Timeline</h2>
        <div class="space-y-4">
            @foreach ($order->statusHistories as $history)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-1 flex-shrink-0"></div>
                        @if (! $loop->last)
                            <div class="w-px flex-1 bg-gray-200 my-1"></div>
                        @endif
                    </div>
                    <div class="pb-3">
                        <p class="text-sm font-medium text-gray-900">{{ $history->status->label() }}</p>
                        @if ($history->comment)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $history->comment }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">{{ $history->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Cancel action --}}
    @if ($order->isCancellable())
        <form method="POST" action="{{ route('orders.cancel', $order) }}"
              onsubmit="return confirm('Are you sure you want to cancel this order?')">
            @csrf
            <button type="submit"
                    class="text-sm text-red-600 border border-red-200 rounded-lg px-4 py-2 hover:bg-red-50 transition">
                Cancel Order
            </button>
        </form>
    @endif

    <a href="{{ route('orders.index') }}" class="inline-block mt-4 text-sm text-indigo-600 hover:underline">
        ← Back to My Orders
    </a>
</div>
@endsection