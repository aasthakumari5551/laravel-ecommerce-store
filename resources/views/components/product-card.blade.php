@props(['product'])

@php
$image = $product->getFirstMediaUrl('product-images')
    ?: $product->primaryImage?->url
    ?: null;
    $onSale     = $product->isOnSale();
    $outOfStock = $product->isOutOfStock();
    $name       = e($product->name);
    $price      = number_format($product->price, 0);
    $oldPrice   = $onSale ? number_format($product->compare_price, 0) : null;
@endphp

<article
class="group relative bg-white rounded-2xl border border-ink-100/80
       overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300
       w-full h-full flex flex-col"
    aria-label="{{ $name }}"
>

    {{-- Image --}}
    <div class="relative overflow-hidden bg-ink-50 aspect-square
                w-full flex items-center justify-center">

        <a href="{{ route('shop.products.show', $product) }}"
           aria-label="View {{ $name }}"
           class="block w-full h-full">

            @if($image)

                <img
                    src="{{ $image }}"
                    alt="{{ $name }}"
                    loading="lazy"
                    decoding="async"
                    width="400"
                    height="400"
                    class="w-full h-full object-cover transition-transform duration-500
                           group-hover:scale-105 {{ $outOfStock ? 'opacity-60' : '' }}"
                >

            @else

                <div class="w-full h-full flex items-center justify-center bg-ink-100">

                    <svg class="w-16 h-16 text-ink-300"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.5"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />

                    </svg>

                </div>

            @endif

        </a>

        {{-- Quick view --}}
        <button
            onclick="window.dispatchEvent(new CustomEvent('quick-view', {
                detail: '{{ $product->uuid }}'
            }))"
            class="absolute inset-x-0 bottom-0 bg-ink-900/80 backdrop-blur-sm
                   text-white text-xs font-semibold py-2
                   opacity-0 group-hover:opacity-100
                   transition-all duration-200
                   translate-y-full group-hover:translate-y-0"
            aria-label="Quick view {{ $name }}"
        >
            Quick View
        </button>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1">

            @if($onSale)
                <span class="bg-red-500 text-white text-[10px]
                             px-2 py-1 rounded-full font-semibold">
                    −{{ $product->discountPercentage() }}%
                </span>
            @endif

            @if($product->is_featured)
                <span class="bg-brand-500 text-white text-[10px]
                             px-2 py-1 rounded-full font-semibold">
                    Featured
                </span>
            @endif

            @if($outOfStock)
                <span class="bg-ink-700 text-white text-[10px]
                             px-2 py-1 rounded-full font-semibold">
                    Sold Out
                </span>
            @endif

        </div>

        {{-- Wishlist --}}
        @auth
            <form method="POST"
                  action="{{ route('wishlist.toggle') }}"
                  data-wishlist-form
                  class="absolute top-3 right-3 opacity-0
                         group-hover:opacity-100 transition-opacity duration-200">

                @csrf

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <button
                    type="submit"
                    class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full
                           shadow flex items-center justify-center
                           hover:bg-white transition-all hover:scale-110"
                    aria-label="Add to wishlist"
                >

                    <svg class="w-4 h-4 text-ink-500"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                        />

                    </svg>

                </button>

            </form>
        @endauth

    </div>

    {{-- Content --}}
    <div class="p-4 space-y-2 flex flex-col flex-1">

        @if($product->category)
            <p class="text-[10px] uppercase tracking-widest
                      text-brand-600 font-bold">
                {{ $product->category->name }}
            </p>
        @endif

        <a href="{{ route('shop.products.show', $product) }}"
           class="text-sm font-semibold text-ink-900
                  hover:text-brand-700 line-clamp-2 transition-colors min-h-[40px]">
            {{ $name }}
        </a>

        @if($product->brand)
            <p class="text-xs text-ink-400">
                {{ $product->brand }}
            </p>
        @endif

        {{-- Ratings --}}
        @if($product->review_count > 0)

            <div class="flex items-center gap-1.5">

                <div class="flex gap-0.5">

                    @for($i = 1; $i <= 5; $i++)

                        <svg
                            class="w-3.5 h-3.5 {{ $i <= round($product->avg_rating)
                                ? 'text-brand-500'
                                : 'text-ink-200' }}"
                            fill="currentColor"
                            viewBox="0 0 20 20">

                            <path d="M9.049 2.927c.3-.921 1.603-.921
                                     1.902 0l1.07 3.292a1 1 0 00.95.69h3.462
                                     c.969 0 1.371 1.24.588 1.81l-2.8
                                     2.034a1 1 0 00-.364 1.118l1.07
                                     3.292c.3.921-.755 1.688-1.54
                                     1.118l-2.8-2.034a1 1 0
                                     00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118
                                     l1.07-3.292a1 1 0 00-.364-1.118L2.98
                                     8.72c-.783-.57-.38-1.81.588-1.81h3.461a1
                                     1 0 00.951-.69l1.07-3.292z"/>

                        </svg>

                    @endfor

                </div>

                <span class="text-xs text-ink-400">
                    ({{ $product->review_count }})
                </span>

            </div>

        @endif

        {{-- Price --}}
        <div class="flex items-center gap-2">

            <span class="text-lg font-bold text-ink-900">
                ₹{{ $price }}
            </span>

            @if($onSale)
                <span class="text-sm text-ink-400 line-through">
                    ₹{{ $oldPrice }}
                </span>
            @endif

        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-2 mt-auto">

            @unless($outOfStock)

                <form method="POST"
                      action="{{ route('cart.store') }}"
                      data-cart-form>

                    @csrf

                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">

                    <button
                        type="submit"
                        class="w-10 h-10 bg-brand-600 hover:bg-brand-700
                               text-white rounded-xl flex items-center justify-center
                               transition-all shadow-sm hover:shadow-md hover:scale-105"
                        aria-label="Add to cart"
                    >

                        <svg class="w-5 h-5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2.5"
                                d="M12 4v16m8-8H4"
                            />

                        </svg>

                    </button>

                </form>

            @else

                <span class="text-xs text-red-500 font-medium">
                    Out of stock
                </span>

            @endunless

            {{-- Compare --}}
            <button
                onclick="window.dispatchEvent(new CustomEvent('compare-toggle', {
                    detail: {
                        uuid: '{{ $product->uuid }}',
                        name: '{{ addslashes($product->name) }}',
                        image: '{{ $product->getFirstMediaUrl('products-images') ?? '' }}'
                    }
                }))"
                class="text-xs text-ink-500 hover:text-brand-600
                       transition-colors flex items-center gap-1"
            >

                <svg class="w-3.5 h-3.5"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0
                           0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6
                           0a2 2 0 002 2h2a2 2 0 002-2m0
                           0V5a2 2 0 012-2h2a2 2 0 012 2v14a2
                           2 0 01-2 2h-2a2 2 0 01-2-2z"
                    />

                </svg>

                Compare

            </button>

        </div>

    </div>

</article>