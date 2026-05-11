@props(['product'])

@php
    $image    = $product->primaryImage?->url ?? null;
    $onSale   = $product->isOnSale();
    $outOfStock = $product->isOutOfStock();
@endphp

<article class="card group flex flex-col overflow-hidden">

    {{-- Image --}}
    <div class="relative overflow-hidden bg-ink-50 aspect-square">
        <a href="{{ route('shop.products.show', $product) }}" class="block w-full h-full">
            @if ($image)
                <img src="{{ $image }}"
                     alt="{{ $product->name }}"
                     loading="lazy"
                     class="w-full h-full object-cover transition-transform duration-500
                            group-hover:scale-105 {{ $outOfStock ? 'opacity-60' : '' }}">
            @else
                <div class="w-full h-full flex items-center justify-center bg-ink-100">
                    <svg class="w-12 h-12 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </a>

        {{-- Badges --}}
        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5">
            @if ($onSale)
                <span class="badge bg-red-500 text-white text-[10px] px-2 py-0.5">
                    −{{ $product->discountPercentage() }}%
                </span>
            @endif
            @if ($product->is_featured)
                <span class="badge bg-brand-500 text-white text-[10px] px-2 py-0.5">
                    Featured
                </span>
            @endif
            @if ($outOfStock)
                <span class="badge bg-ink-500 text-white text-[10px] px-2 py-0.5">
                    Sold Out
                </span>
            @endif
        </div>

        {{-- Wishlist button --}}
        @auth
            <form method="POST" action="{{ route('wishlist.toggle') }}"
                  class="absolute top-2.5 right-2.5 opacity-0 group-hover:opacity-100
                         transition-opacity duration-200">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                        class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full shadow
                               flex items-center justify-center hover:bg-white transition-colors">
                    <svg class="w-4 h-4 text-ink-600 hover:text-red-500 transition-colors"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
            </form>
        @endauth
    </div>

    {{-- Info --}}
    <div class="flex flex-col flex-1 p-3.5">
        @if ($product->category)
            <p class="text-[10px] font-semibold uppercase tracking-widest text-brand-600 mb-1">
                {{ $product->category->name }}
            </p>
        @endif

        <a href="{{ route('shop.products.show', $product) }}"
           class="text-sm font-medium text-ink-900 hover:text-brand-700 leading-snug line-clamp-2
                  transition-colors mb-auto">
            {{ $product->name }}
        </a>

        {{-- Stars --}}
        @if ($product->review_count > 0)
            <div class="flex items-center gap-1.5 mt-2">
                <div class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3 {{ $i <= round($product->avg_rating) ? 'text-brand-500' : 'text-ink-200' }}"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
                <span class="text-[11px] text-ink-400">({{ $product->review_count }})</span>
            </div>
        @endif

        {{-- Price + Add to cart --}}
        <div class="flex items-center justify-between mt-2.5 gap-2">
            <div>
                <span class="text-base font-bold text-ink-900">
                    ₹{{ number_format($product->price, 0) }}
                </span>
                @if ($onSale)
                    <span class="text-xs text-ink-400 line-through ml-1.5">
                        ₹{{ number_format($product->compare_price, 0) }}
                    </span>
                @endif
            </div>

            @unless ($outOfStock)
                <form method="POST" action="{{ route('cart.store') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity"   value="1">
                    <button type="submit"
                            class="flex-shrink-0 w-8 h-8 bg-brand-600 hover:bg-brand-700
                                   text-white rounded-lg flex items-center justify-center
                                   transition-colors shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </form>
            @endunless
        </div>
    </div>
</article>