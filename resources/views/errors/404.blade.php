@extends('layouts.app')
@section('title', 'Page Not Found')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16">
    <div class="text-center max-w-md mx-auto">
        {{-- Animated illustration --}}
        <div class="relative inline-block mb-8">
            <div class="w-40 h-40 bg-brand-50 rounded-full flex items-center
                        justify-center mx-auto">
                <span class="font-display text-7xl text-brand-300 leading-none
                             select-none">
                    404
                </span>
            </div>
            <div class="absolute -top-2 -right-2 w-12 h-12 bg-ink-100 rounded-full
                        flex items-center justify-center animate-bounce">
                <span class="text-2xl">🔍</span>
            </div>
        </div>

        <h1 class="font-display text-3xl text-ink-900 mb-3">
            Page Not Found
        </h1>
        <p class="text-ink-500 text-sm leading-relaxed mb-8">
            The page you're looking for has moved, been removed, or doesn't exist.
            Let's get you back on track.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="btn-primary">
                Back to Home
            </a>
            <a href="{{ route('shop.products.index') }}" class="btn-secondary">
                Browse Products
            </a>
        </div>

        {{-- Quick links --}}
        <div class="mt-10 pt-6 border-t border-ink-100">
            <p class="text-xs text-ink-400 mb-3 font-medium uppercase tracking-wider">
                Popular Pages
            </p>
            <div class="flex flex-wrap gap-2 justify-center">
                @foreach([
                    ['label' => 'New Arrivals',  'href' => route('shop.products.index', ['sort' => 'newest'])],
                    ['label' => 'Best Sellers',  'href' => route('shop.products.index', ['sort' => 'popular'])],
                    ['label' => 'My Orders',     'href' => route('orders.index')],
                    ['label' => 'My Cart',       'href' => route('cart.index')],
                ] as $link)
                    <a href="{{ $link['href'] }}"
                       class="px-3 py-1.5 bg-ink-50 hover:bg-brand-50 border border-ink-200
                              hover:border-brand-300 text-ink-600 hover:text-brand-700
                              rounded-full text-xs font-medium transition-all">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection