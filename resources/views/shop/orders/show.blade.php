@extends('layouts.app')
@section('title', 'Order ' . $order->number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-7">
        <a href="{{ route('orders.index') }}" class="btn-ghost p-2 -ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="text-xs text-ink-400 font-mono mb-0.5">{{ $order->number }}</p>
            <h1 class="font-display text-2xl text-ink-900 leading-tight">Order Details</h1>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <span class="badge bg-{{ $order->status->color() }}-100
                         text-{{ $order->status->color() }}-700 text-sm px-3 py-1.5">
                {{ $order->status->label() }}
            </span>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm mb-5">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->has('cancel'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-5">
            {{ $errors->first('cancel') }}
        </div>
    @endif

    {{-- Status timeline --}}
    <div class="card p-5 sm:p-6 mb-5">
        <h2 class="font-semibold text-ink-900 text-sm mb-5">Order Timeline</h2>
        <div class="flex items-start gap-0 overflow-x-auto scrollbar-none pb-1">
            @foreach($order->statusHistories->reverse() as $i => $history)
                <div class="flex items-start gap-0 flex-shrink-0
                             {{ !$loop->last ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $loop->first
                                        ? 'bg-brand-600 text-white shadow-md shadow-brand-200'
                                        : 'bg-ink-100 text-ink-400' }}">
                            @if($loop->first)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                </svg>
                            @else
                                <div class="w-2 h-2 bg-ink-300 rounded-full"></div>
                            @endif
                        </div>
                        <p class="text-xs font-semibold mt-2 text-center whitespace-nowrap
                                   {{ $loop->first ? 'text-brand-700' : 'text-ink-500' }}">
                            {{ $history->status->label() }}
                        </p>
                        <p class="text-[10px] text-ink-400 text-center mt-0.5 whitespace-nowrap">
                            {{ $history->created_at->format('d M, h:i A') }}
                        </p>
                    </div>
                    @if(!$loop->last)
                        <div class="flex-1 h-px bg-ink-200 mt-4 mx-1 min-w-[24px]"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Items --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-ink-100">
                    <h2 class="font-semibold text-ink-900 text-sm">
                        Items Ordered ({{ $order->items->count() }})
                    </h2>
                </div>
                <div class="divide-y divide-ink-50">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4 px-5 py-4">
                            <div class="w-14 h-14 rounded-xl bg-ink-50 border border-ink-100
                                        overflow-hidden flex-shrink-0">
                                @if($item->product?->primaryImage)
                                    <img src="{{ $item->product->primaryImage->url }}"
                                         alt="{{ $item->product_name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink-900 line-clamp-1">
                                    {{ $item->product_name }}
                                </p>
                                @if($item->product_sku)
                                    <p class="text-xs font-mono text-ink-400">{{ $item->product_sku }}</p>
                                @endif
                                <p class="text-xs text-ink-500 mt-0.5">
                                    × {{ $item->quantity }} @ ₹{{ number_format($item->unit_price, 0) }}
                                </p>
                            </div>
                            <p class="font-semibold text-ink-900 text-sm flex-shrink-0">
                                ₹{{ number_format($item->subtotal, 0) }}
                            </p>
                        </div>
                    @endforeach
                </div>
                {{-- Totals --}}
                <div class="bg-ink-50 px-5 py-4 space-y-2 text-sm">
                    <div class="flex justify-between text-ink-500">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>Shipping</span>
                        <span>{{ $order->shipping_amount == 0 ? 'Free' : '₹'.number_format($order->shipping_amount, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>Tax</span>
                        <span>₹{{ number_format($order->tax_amount, 0) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-700 font-medium">
                            <span>Discount</span>
                            <span>−₹{{ number_format($order->discount_amount, 0) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-ink-900 text-base
                                border-t border-ink-200 pt-2">
                        <span>Total</span>
                        <span>₹{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Cancel button --}}
            @if($order->isCancellable())
                <form method="POST" action="{{ route('orders.cancel', $order) }}"
                      onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button type="submit"
                            class="text-sm text-red-600 border border-red-200 rounded-xl px-4 py-2.5
                                   hover:bg-red-50 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Order
                    </button>
                </form>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Delivery address --}}
            <div class="card p-5">
                <h3 class="font-semibold text-ink-900 text-sm mb-3">Deliver To</h3>
                <div class="space-y-0.5 text-sm text-ink-600">
                    <p class="font-semibold text-ink-900">
                        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}
                    </p>
                    <p>{{ $order->shipping_line1 }}</p>
                    @if($order->shipping_line2)
                        <p>{{ $order->shipping_line2 }}</p>
                    @endif
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}</p>
                    <p class="text-ink-400 text-xs mt-1">📞 {{ $order->shipping_phone }}</p>
                </div>
            </div>

            {{-- Payment --}}
            <div class="card p-5">
                <h3 class="font-semibold text-ink-900 text-sm mb-3">Payment</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-ink-500">Status</span>
                        <span class="{{ $order->isPaid() ? 'text-green-600 font-semibold' : 'text-ink-600' }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-500">Method</span>
                        <span class="text-ink-600 capitalize font-mono text-xs">
                            {{ str_replace('_', ' ', $order->payment_method) }}
                        </span>
                    </div>
                    @if($order->paid_at)
                        <div class="flex justify-between">
                            <span class="text-ink-500">Paid at</span>
                            <span class="text-ink-600 text-xs">
                                {{ $order->paid_at->format('d M Y, h:i A') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Need help --}}
            <div class="bg-brand-50 border border-brand-100 rounded-xl p-4">
                <p class="text-sm font-semibold text-brand-800 mb-1">Need help?</p>
                <p class="text-xs text-brand-600 leading-relaxed">
                    Issues with your order? Our support team is here for you.
                </p>
                <a href="mailto:support@store.com"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700
                          hover:text-brand-900 mt-2 transition-colors">
                    Contact Support →
                </a>
            </div>
        </div>
    </div>
</div>
@endsection