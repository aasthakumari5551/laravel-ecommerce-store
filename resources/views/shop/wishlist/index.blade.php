@extends('layouts.app')
@section('title', 'Wishlist')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-7">
        <div class="flex items-center gap-3">
            <h1 class="font-display text-3xl text-ink-900">Wishlist</h1>
            @if($wishlist->items->isNotEmpty())
                <span class="badge bg-brand-100 text-brand-700 text-sm px-3 py-1">
                    {{ $wishlist->totalItems() }} items
                </span>
            @endif
        </div>
        @if($wishlist->items->isNotEmpty())
            <form method="POST" action="{{ route('wishlist.clear') }}"
                  onsubmit="return confirm('Clear your wishlist?')">
                @csrf
                <button type="submit"
                        class="text-xs text-ink-400 hover:text-red-500 transition-colors font-medium">
                    Clear all
                </button>
            </form>
        @endif
    </div>

    @if($wishlist->products->isEmpty())
        <x-empty-state
            icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
            title="Your wishlist is empty"
            message="Save products you love by tapping the heart icon."
            :action="route('shop.products.index')"
            action-label="Discover Products"
        />
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($wishlist->products as $i => $product)
                <div class="relative" data-aos style="animation-delay:{{ $i * 0.05 }}s">
                    <x-product-card :product="$product" />
                    {{-- Remove overlay --}}
                    <form method="POST" action="{{ route('wishlist.destroy', $product->id) }}"
                          class="absolute top-2 left-2">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="w-7 h-7 bg-white/90 backdrop-blur-sm rounded-lg shadow
                                       flex items-center justify-center text-red-400
                                       hover:text-red-600 hover:bg-white transition-all">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection