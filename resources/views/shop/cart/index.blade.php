@extends('layouts.app')
@section('title', 'Your Cart')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="cartPage()">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <h1 class="font-display text-3xl text-ink-900">Your Cart</h1>
        @if(!$cart['is_empty'])
            <span class="badge bg-brand-100 text-brand-700 text-sm px-3 py-1">
                {{ $cart['total_items'] }} item{{ $cart['total_items'] !== 1 ? 's' : '' }}
            </span>
        @endif
    </div>

    @if($cart['is_empty'])
        {{-- Empty state --}}
        <div class="text-center py-24">
            <div class="w-24 h-24 bg-ink-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-11 h-11 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="font-display text-2xl text-ink-800 mb-2">Your cart is empty</p>
            <p class="text-ink-400 text-sm mb-7">Looks like you haven't added anything yet.</p>
            <a href="{{ route('shop.products.index') }}" class="btn-primary text-base px-8 py-3">
                Start Shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Cart Items ──────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-3">

                {{-- Alerts --}}
                @if($errors->has('stock'))
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                        {{ $errors->first('stock') }}
                    </div>
                @endif

                @foreach($cart['items'] as $item)
                    <div class="card p-4 sm:p-5 flex gap-4 group animate-fade-in">

                        {{-- Image --}}
                        <a href="{{ route('shop.products.show', $item->product) }}"
                           class="flex-shrink-0 w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-ink-50 border border-ink-100">
                            @if($item->product->primaryImage)
                                <img src="{{ $item->product->primaryImage->url }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-ink-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    @if($item->product->category)
                                        <p class="text-[10px] font-semibold uppercase tracking-widest
                                                   text-brand-600 mb-0.5">
                                            {{ $item->product->category->name }}
                                        </p>
                                    @endif
                                    <a href="{{ route('shop.products.show', $item->product) }}"
                                       class="font-medium text-ink-900 text-sm sm:text-base
                                              hover:text-brand-700 transition-colors line-clamp-2">
                                        {{ $item->product->name }}
                                    </a>
                                    <p class="text-xs text-ink-400 mt-0.5 font-mono">
                                        ₹{{ number_format($item->unit_price, 2) }} each
                                    </p>
                                </div>
                                {{-- Remove --}}
                                <form method="POST"
                                      action="{{ route('cart.destroy', $item->id) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="flex-shrink-0 w-7 h-7 rounded-lg flex items-center
                                                   justify-center text-ink-300 hover:text-red-500
                                                   hover:bg-red-50 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Qty + subtotal --}}
                            <div class="flex items-center justify-between mt-3">
                                <form method="POST"
                                      action="{{ route('cart.update', $item->id) }}"
                                      class="flex items-center border border-ink-200 rounded-xl overflow-hidden">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="quantity"
                                            value="{{ max(1, $item->quantity - 1) }}"
                                            class="w-8 h-8 flex items-center justify-center text-ink-500
                                                   hover:bg-ink-50 transition-colors text-base font-medium">
                                        −
                                    </button>
                                    <span class="w-8 text-center text-sm font-semibold text-ink-900">
                                        {{ $item->quantity }}
                                    </span>
                                    <button type="submit" name="quantity"
                                            value="{{ $item->quantity + 1 }}"
                                            class="w-8 h-8 flex items-center justify-center text-ink-500
                                                   hover:bg-ink-50 transition-colors text-base font-medium">
                                        +
                                    </button>
                                </form>

                                <p class="font-bold text-ink-900 text-base">
                                    ₹{{ number_format($item->lineTotal(), 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Clear cart --}}
                <div class="flex justify-end pt-1">
                    <form method="POST" action="{{ route('cart.clear') }}"
                          onsubmit="return confirm('Clear your entire cart?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-xs text-ink-400 hover:text-red-500 transition-colors font-medium">
                            Clear cart
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Order Summary ───────────────────────────── --}}
            <div class="space-y-4">
                {{-- Coupon --}}
                <div class="card p-5" x-data="{ open: {{ session('coupon_code') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="flex items-center justify-between w-full text-sm font-semibold
                                   text-ink-800">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Promo Code
                        </span>
                        <svg class="w-4 h-4 text-ink-400 transition-transform" :class="open ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-collapse class="mt-3">
                        @if(session('coupon_code'))
                            <div class="flex items-center justify-between bg-green-50 border
                                        border-green-200 rounded-xl px-3 py-2.5 mb-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/>
                                    </svg>
                                    <span class="text-sm font-semibold text-green-800 font-mono">
                                        {{ session('coupon_code') }}
                                    </span>
                                </div>
                                <form method="POST" action="{{ route('coupons.remove') }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs text-green-600 hover:text-red-500 transition-colors">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            @if($errors->has('coupon'))
                                <p class="text-xs text-red-600 mb-2">{{ $errors->first('coupon') }}</p>
                            @endif
                            <form method="POST" action="{{ route('coupons.apply') }}"
                                  class="flex gap-2">
                                @csrf
                                <input type="text" name="code"
                                       placeholder="Enter code"
                                       class="input text-sm flex-1 uppercase tracking-widest">
                                <button type="submit" class="btn-primary text-sm flex-shrink-0">
                                    Apply
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Summary card --}}
                <div class="card p-5 space-y-3">
                    <h2 class="font-semibold text-ink-900 text-base mb-1">Order Summary</h2>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-ink-500">Subtotal</span>
                            <span class="font-medium text-ink-900">
                                ₹{{ number_format($cart['subtotal'], 0) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-500">Shipping</span>
                            <span class="font-medium text-ink-600">
                                @if($cart['subtotal'] >= 999)
                                    <span class="text-green-600 font-semibold">Free</span>
                                @else
                                    ₹99
                                    <span class="text-ink-400 text-xs ml-1">(free on ₹999+)</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-ink-500">GST (18%)</span>
                            <span class="font-medium text-ink-900">
                                ₹{{ number_format($cart['subtotal'] * 0.18, 0) }}
                            </span>
                        </div>
                        @if(session('coupon_code'))
                            <div class="flex justify-between text-green-700">
                                <span class="font-medium">Discount</span>
                                <span class="font-semibold">Applied ✓</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-ink-100 pt-3 flex justify-between items-baseline">
                        <span class="font-bold text-ink-900">Estimated Total</span>
                        <span class="font-bold text-xl text-ink-900">
                            ₹{{ number_format(
                                $cart['subtotal']
                                + ($cart['subtotal'] >= 999 ? 0 : 99)
                                + ($cart['subtotal'] * 0.18),
                                0) }}
                        </span>
                    </div>

                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn-primary w-full justify-center py-3 mt-1">
                            Proceed to Checkout
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary w-full justify-center py-3 mt-1">
                            Sign In to Checkout
                        </a>
                    @endauth

                    <a href="{{ route('shop.products.index') }}"
                       class="block text-center text-xs text-ink-400 hover:text-ink-700 transition-colors mt-1">
                        ← Continue Shopping
                    </a>
                </div>

                {{-- Trust --}}
                <div class="flex flex-col gap-2 px-1">
                    @foreach([
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                          'text' => 'Secure SSL checkout'],
                        ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                          'text' => 'Easy 30-day returns'],
                    ] as $t)
                        <div class="flex items-center gap-2 text-xs text-ink-400">
                            <svg class="w-3.5 h-3.5 text-brand-500 flex-shrink-0"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/>
                            </svg>
                            {{ $t['text'] }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function cartPage() { return {} }
</script>
@endpush
@endsection