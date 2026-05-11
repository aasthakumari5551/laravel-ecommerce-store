@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('cart.index') }}" class="btn-ghost p-2 -ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-display text-3xl text-ink-900">Checkout</h1>
    </div>

    {{-- Progress steps --}}
    <div class="flex items-center gap-0 mb-10">
        @foreach([['num' => 1, 'label' => 'Address'], ['num' => 2, 'label' => 'Payment'], ['num' => 3, 'label' => 'Confirm']] as $step)
            <div class="flex items-center {{ !$loop->last ? 'flex-1' : '' }}">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                {{ $step['num'] === 1 ? 'bg-brand-600 text-white' : 'bg-ink-100 text-ink-400' }}">
                        {{ $step['num'] }}
                    </div>
                    <span class="text-sm font-medium hidden sm:block
                                 {{ $step['num'] === 1 ? 'text-ink-900' : 'text-ink-400' }}">
                        {{ $step['label'] }}
                    </span>
                </div>
                @if(!$loop->last)
                    <div class="flex-1 h-px bg-ink-200 mx-3"></div>
                @endif
            </div>
        @endforeach
    </div>

    @if($errors->has('checkout'))
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm mb-6">
            {{ $errors->first('checkout') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left: Address selection ────────────────── --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('checkout.initiate') }}" id="checkout-form">
                @csrf

                <div class="card p-6 mb-5">
                    <h2 class="font-semibold text-ink-900 mb-4 flex items-center gap-2">
                        <span class="w-6 h-6 bg-brand-100 text-brand-700 rounded-full flex items-center
                                     justify-center text-xs font-bold">1</span>
                        Delivery Address
                    </h2>

                    @if($addresses->isEmpty())
                        <div class="text-center py-8 bg-ink-50 rounded-xl">
                            <p class="text-ink-500 text-sm mb-3">No saved addresses yet.</p>
                            <a href="{{ route('addresses.index') }}"
                               class="btn-primary text-sm">
                                Add Address
                            </a>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($addresses as $address)
                                <label class="flex items-start gap-3 cursor-pointer p-4 rounded-xl border-2
                                              transition-all has-[:checked]:border-brand-500
                                              has-[:checked]:bg-brand-50 border-ink-100 hover:border-ink-300">
                                    <input type="radio" name="address_id"
                                           value="{{ $address->id }}"
                                           {{ ($address->is_default || $loop->first) ? 'checked' : '' }}
                                           class="mt-0.5 accent-brand-600 flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="font-semibold text-ink-900 text-sm">
                                                {{ $address->fullName() }}
                                            </span>
                                            <span class="badge bg-ink-100 text-ink-500 text-[10px] px-2 py-0.5">
                                                {{ $address->label }}
                                            </span>
                                            @if($address->is_default)
                                                <span class="badge bg-brand-100 text-brand-700 text-[10px] px-2 py-0.5">
                                                    Default
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-ink-600">{{ $address->oneLiner() }}</p>
                                        <p class="text-xs text-ink-400 mt-0.5">📞 {{ $address->phone }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <a href="{{ route('addresses.index') }}"
                           class="inline-flex items-center gap-1.5 text-sm text-brand-600
                                  hover:text-brand-700 mt-4 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                            Add new address
                        </a>
                    @endif
                </div>

                {{-- Notes --}}
                <div class="card p-6 mb-5">
                    <h2 class="font-semibold text-ink-900 mb-3 flex items-center gap-2">
                        <span class="w-6 h-6 bg-ink-100 text-ink-500 rounded-full flex items-center
                                     justify-center text-xs font-bold">2</span>
                        Order Notes
                        <span class="text-xs text-ink-400 font-normal">(optional)</span>
                    </h2>
                    <textarea name="notes" rows="2"
                              placeholder="Any special instructions for delivery…"
                              class="input text-sm resize-none">{{ old('notes') }}</textarea>
                </div>

                <button type="submit"
                        class="btn-primary w-full justify-center py-3.5 text-base rounded-xl
                               {{ $addresses->isEmpty() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                    Continue to Payment
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- ── Right: Order summary ────────────────────── --}}
        <div class="space-y-4">
            <div class="card p-5">
                <h3 class="font-semibold text-ink-900 text-sm mb-4">
                    Order Summary
                    <span class="text-ink-400 font-normal">({{ $cart['total_items'] }} items)</span>
                </h3>

                <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                    @foreach($cart['items'] as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg overflow-hidden bg-ink-50 border
                                        border-ink-100 flex-shrink-0">
                                @if($item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->url }}"
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-ink-900 line-clamp-1">
                                    {{ $item->product->name }}
                                </p>
                                <p class="text-xs text-ink-400">× {{ $item->quantity }}</p>
                            </div>
                            <p class="text-xs font-semibold text-ink-900 flex-shrink-0">
                                ₹{{ number_format($item->lineTotal(), 0) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-ink-100 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between text-ink-500">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($cart['subtotal'], 0) }}</span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>Shipping</span>
                        <span class="{{ $cart['subtotal'] >= 999 ? 'text-green-600 font-semibold' : '' }}">
                            {{ $cart['subtotal'] >= 999 ? 'Free' : '₹99' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-ink-500">
                        <span>GST (18%)</span>
                        <span>₹{{ number_format($cart['subtotal'] * 0.18, 0) }}</span>
                    </div>
                    @if(session('coupon_code'))
                        <div class="flex justify-between text-green-700 font-medium">
                            <span>Coupon ({{ session('coupon_code') }})</span>
                            <span>Applied ✓</span>
                        </div>
                    @endif
                    <div class="flex justify-between font-bold text-ink-900 text-base
                                border-t border-ink-100 pt-2 mt-1">
                        <span>Total</span>
                        <span>₹{{ number_format(
                            $cart['subtotal']
                            + ($cart['subtotal'] >= 999 ? 0 : 99)
                            + ($cart['subtotal'] * 0.18),
                            0) }}</span>
                    </div>
                </div>
            </div>

            {{-- Secure badge --}}
            <div class="flex items-center gap-2.5 px-1">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-xs text-ink-400">Your order is protected by 256-bit SSL encryption</p>
            </div>
        </div>
    </div>
</div>
@endsection