@extends('layouts.app')

@section('title', $product->name)
@section('meta_description', $product->short_description ?? $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-7">
        <a href="{{ url('/') }}" class="hover:text-ink-700 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('shop.products.index') }}" class="hover:text-ink-700 transition-colors">Products</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('shop.products.index', ['category' => $product->category->slug]) }}"
               class="hover:text-ink-700 transition-colors capitalize">
                {{ $product->category->name }}
            </a>
        @endif
        <span>/</span>
        <span class="text-ink-700 font-medium truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    {{-- ══════════════════════════════════════════════════
         PRODUCT DETAIL
    ══════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-14">

        <x-product-gallery :product="$product" />

        {{-- ── PRODUCT INFO ───────────────────────────── --}}
        <div class="flex flex-col" x-data="{ qty: 1 }">

            {{-- Category tag --}}
            @if($product->category)
                <a href="{{ route('shop.products.index', ['category' => $product->category->slug]) }}"
                   class="text-xs font-semibold uppercase tracking-widest text-brand-600
                          hover:text-brand-700 transition-colors mb-2">
                    {{ $product->category->name }}
                </a>
            @endif

            <h1 class="font-display text-2xl sm:text-3xl text-ink-900 leading-tight mb-3">
                {{ $product->name }}
            </h1>

            {{-- Rating summary --}}
            @if($product->review_count > 0)
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex items-center gap-1">
                        @for($s = 1; $s <= 5; $s++)
                            <svg class="w-4 h-4 {{ $s <= round($product->avg_rating) ? 'text-brand-500' : 'text-ink-200' }}"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-ink-900">
                        {{ number_format($product->avg_rating, 1) }}
                    </span>
                    <a href="#reviews" class="text-sm text-ink-400 hover:text-brand-600 transition-colors">
                        {{ $product->review_count }} review{{ $product->review_count !== 1 ? 's' : '' }}
                    </a>
                    @if($product->sku)
                        <span class="text-ink-200">·</span>
                        <span class="text-xs text-ink-400 font-mono">SKU: {{ $product->sku }}</span>
                    @endif
                </div>
            @endif

            {{-- Price --}}
            <div class="flex items-baseline gap-3 mb-5">
                <span class="text-3xl font-bold text-ink-900">
                    ₹{{ number_format($product->price, 0) }}
                </span>
                @if($product->isOnSale())
                    <span class="text-lg text-ink-400 line-through">
                        ₹{{ number_format($product->compare_price, 0) }}
                    </span>
                    <span class="badge bg-green-100 text-green-700 text-xs px-2.5 py-1">
                        Save ₹{{ number_format($product->compare_price - $product->price, 0) }}
                    </span>
                @endif
            </div>

            {{-- Short description --}}
            @if($product->short_description)
                <p class="text-ink-600 text-sm leading-relaxed mb-5">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Stock status --}}
<div class="flex items-center gap-2 mb-3">
    @if($product->isOutOfStock())
        <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
        <span class="text-sm text-red-600 font-medium">Out of Stock</span>
    @elseif($product->isLowStock())
        <span class="w-2 h-2 rounded-full bg-orange-400 animate-pulse flex-shrink-0"></span>
        <span class="text-sm text-orange-600 font-medium">
            Only {{ $product->stock }} left — order soon!
        </span>
    @elseif($product->stock <= 50)
        <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
        <span class="text-sm text-green-700 font-medium">
            In Stock — {{ $product->stock }} units available
        </span>
    @else
        <span class="w-2 h-2 rounded-full bg-green-400 flex-shrink-0"></span>
        <span class="text-sm text-green-700 font-medium">In Stock</span>
    @endif
</div>

{{-- Stock progress bar (urgency) --}}
@if(!$product->isOutOfStock() && $product->stock <= 50 && $product->track_inventory)
    <div class="mb-4">
        <div class="w-full bg-ink-100 rounded-full h-1.5 overflow-hidden">
            <div class="h-1.5 rounded-full transition-all
                        {{ $product->stock <= 10 ? 'bg-red-500' : 'bg-green-500' }}"
                 style="width:{{ min(100, ($product->stock / 50) * 100) }}%">
            </div>
        </div>
        @if($product->stock <= 10)
            <p class="text-xs text-red-500 mt-1 font-medium">
                🔥 Selling fast — only {{ $product->stock }} remaining
            </p>
        @endif
    </div>
@endif

{{-- Delivery estimate --}}
<div class="bg-ink-50 rounded-xl p-4 mb-5 space-y-2.5 border border-ink-100">
    <div class="flex items-start gap-3">
        <svg class="w-4 h-4 text-green-600 flex-shrink-0 mt-0.5" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-ink-900">
                Free Delivery by {{ $delivery['standard'] }}
            </p>
            <p class="text-xs text-ink-500 mt-0.5">{{ $delivery['order_by'] }}</p>
        </div>
    </div>
    <div class="flex items-start gap-3">
        <svg class="w-4 h-4 text-brand-600 flex-shrink-0 mt-0.5" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-ink-900">
                Express Delivery by {{ $delivery['express'] }}
            </p>
            <p class="text-xs text-ink-500 mt-0.5">Premium shipping available at checkout</p>
        </div>
    </div>
    <div class="flex items-start gap-3">
        <svg class="w-4 h-4 text-ink-400 flex-shrink-0 mt-0.5" fill="none"
             stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <p class="text-xs text-ink-500">30-day hassle-free returns & exchanges</p>
    </div>
</div>

            {{-- ── STICKY ADD TO CART (desktop inline, mobile sticky bar) ── --}}
            @unless($product->isOutOfStock())
                <div class="space-y-3 mb-6">
                    {{-- Quantity picker --}}
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-ink-700">Qty:</span>
                        <div class="flex items-center border border-ink-200 rounded-xl overflow-hidden">
                            <button type="button" @click="qty = Math.max(1, qty - 1)"
                                    class="w-10 h-10 flex items-center justify-center
                                           text-ink-600 hover:bg-ink-50 transition-colors text-lg font-medium">
                                −
                            </button>
                            <span class="w-10 text-center text-sm font-semibold text-ink-900"
                                  x-text="qty"></span>
                            <button type="button"
                                    @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                                    class="w-10 h-10 flex items-center justify-center
                                           text-ink-600 hover:bg-ink-50 transition-colors text-lg font-medium">
                                +
                            </button>
                        </div>
                        @if($product->track_inventory)
                            <span class="text-xs text-ink-400">{{ $product->stock }} available</span>
                        @endif
                    </div>

                    {{-- Add to cart button --}}
                    <form method="POST" action="{{ route('cart.store') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" :value="qty">
                        <button type="submit"
                                class="btn-primary w-full justify-center text-base py-3.5 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            Add to Cart
                        </button>
                    </form>

                    {{-- Wishlist --}}
                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn-secondary w-full justify-center py-2.5 rounded-xl text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Save to Wishlist
                            </button>
                        </form>
                    @endauth
                </div>
            @else
                <div class="bg-ink-50 rounded-xl px-5 py-4 mb-6 text-center">
                    <p class="text-ink-500 text-sm">This product is currently unavailable.</p>
                    <a href="{{ route('shop.products.index') }}"
                       class="btn-primary mt-3 inline-flex text-sm">
                        Browse Similar Products
                    </a>
                </div>
            @endunless

            {{-- Trust pills --}}
            <div class="flex flex-wrap gap-2 py-4 border-t border-ink-100">
                @foreach([
                    ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'text' => 'Free delivery ₹999+'],
                    ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'text' => '30-day returns'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'text' => 'Secure checkout'],
                ] as $t)
                    <span class="inline-flex items-center gap-1.5 text-xs text-ink-500">
                        <svg class="w-3.5 h-3.5 text-brand-500 flex-shrink-0"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/>
                        </svg>
                        {{ $t['text'] }}
                    </span>
                @endforeach
            </div>

            {{-- Share --}}
<div class="flex items-center gap-2 pt-1">

    <x-share-product :product="$product" />

</div>

{{-- Pincode checker --}}
<x-pincode-checker />
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         DESCRIPTION TABS
    ══════════════════════════════════════════════════ --}}
    <div class="mt-12" x-data="{ tab: 'description' }">
        <div class="flex gap-0 border-b border-ink-200 mb-6">
            @foreach([['key' => 'description', 'label' => 'Description'], ['key' => 'details', 'label' => 'Details']] as $t)
                <button @click="tab = '{{ $t['key'] }}'"
                        class="px-5 py-3 text-sm font-medium border-b-2 transition-all -mb-px"
                        :class="tab === '{{ $t['key'] }}'
                            ? 'border-brand-600 text-brand-700'
                            : 'border-transparent text-ink-500 hover:text-ink-800'">
                    {{ $t['label'] }}
                </button>
            @endforeach
        </div>

        <div x-show="tab === 'description'" class="prose prose-sm max-w-none text-ink-700 leading-relaxed">
            @if($product->description)
                {!! nl2br(e($product->description)) !!}
            @else
                <p class="text-ink-400">No description available.</p>
            @endif
        </div>
        <div x-show="tab === 'details'" class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
            @foreach([
                ['label' => 'SKU',       'value' => $product->sku       ?? '—'],
                ['label' => 'Category',  'value' => $product->category?->name ?? '—'],
                ['label' => 'In Stock',  'value' => $product->track_inventory ? $product->stock.' units' : 'Unlimited'],
                ['label' => 'Rating',    'value' => $product->review_count > 0 ? number_format($product->avg_rating,1).' / 5' : 'No reviews'],
            ] as $row)
                <div class="flex justify-between py-2.5 border-b border-ink-100 text-sm">
                    <span class="text-ink-500 font-medium">{{ $row['label'] }}</span>
                    <span class="text-ink-900">{{ $row['value'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         REVIEWS SECTION
    ══════════════════════════════════════════════════ --}}
    <div id="reviews" class="mt-14 scroll-mt-24">

        <div class="flex items-end justify-between mb-7">
            <div>
                <h2 class="section-title">Customer Reviews</h2>
                @if($product->review_count > 0)
                    <div class="flex items-center gap-2 mt-1.5">
                        <div class="flex">
                            @for($s = 1; $s <= 5; $s++)
                                <svg class="w-4 h-4 {{ $s <= round($product->avg_rating) ? 'text-brand-500' : 'text-ink-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <span class="font-bold text-ink-900">{{ number_format($product->avg_rating,1) }}</span>
                        <span class="text-ink-400 text-sm">based on {{ $product->review_count }} reviews</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Write review form --}}
        @auth
            <div class="card p-6 mb-8" x-data="{ rating: 0, hover: 0 }">
                <h3 class="font-semibold text-ink-900 mb-4">Write a Review</h3>
                @if($errors->has('review'))
                    <p class="text-red-600 text-sm mb-3">{{ $errors->first('review') }}</p>
                @endif
                <form method="POST" action="{{ route('reviews.store', $product) }}">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm font-medium text-ink-700 mb-2">Your Rating</p>
                        <div class="flex gap-1.5">
                            @for($s = 1; $s <= 5; $s++)
                                <button type="button"
                                        @mouseenter="hover = {{ $s }}"
                                        @mouseleave="hover = 0"
                                        @click="rating = {{ $s }}"
                                        class="transition-transform hover:scale-110">
                                    <svg class="w-7 h-7 transition-colors"
                                         :class="(hover || rating) >= {{ $s }} ? 'text-brand-500' : 'text-ink-200'"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-ink-700 mb-1.5">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="Summary of your review"
                                   class="input text-sm">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-ink-700 mb-1.5">Review</label>
                            <textarea name="body" rows="3"
                                      placeholder="Share your experience with this product…"
                                      class="input text-sm resize-none">{{ old('body') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary text-sm">
                        Submit Review
                    </button>
                </form>
            </div>
        @else
            <div class="card p-6 mb-8 flex items-center justify-between gap-4">
                <p class="text-sm text-ink-600">Sign in to leave a review.</p>
                <a href="{{ route('login') }}" class="btn-primary text-sm flex-shrink-0">Sign In</a>
            </div>
        @endauth

        {{-- Review list --}}
        @if($product->approvedReviews->isEmpty())
            <div class="text-center py-12 bg-ink-50 rounded-2xl">
                <p class="text-ink-400 text-sm">No reviews yet. Be the first!</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($product->approvedReviews as $review)
                    <div class="card p-5 sm:p-6" data-aos>
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-full bg-brand-100 flex items-center
                                            justify-center text-brand-700 text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-ink-900 text-sm">{{ $review->user->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <div class="flex">
                                            @for($s = 1; $s <= 5; $s++)
                                                <svg class="w-3.5 h-3.5 {{ $s <= $review->rating ? 'text-brand-500' : 'text-ink-200' }}"
                                                     fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        @if($review->is_verified_purchase)
                                            <span class="badge bg-green-50 text-green-700 text-[10px] px-2 py-0.5">
                                                ✓ Verified Purchase
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs text-ink-400 flex-shrink-0">
                                {{ $review->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($review->title)
                            <p class="font-semibold text-ink-900 text-sm mt-3">{{ $review->title }}</p>
                        @endif
                        @if($review->body)
                            <p class="text-ink-600 text-sm mt-1.5 leading-relaxed">{{ $review->body }}</p>
                        @endif
                        @auth
                            @if(auth()->id() === $review->user_id)
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                                      class="mt-3" onsubmit="return confirm('Delete this review?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-red-500 hover:text-red-700 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ═══ CUSTOMERS ALSO BOUGHT ═══ --}}
@if($alsoOrdered->isNotEmpty())

    <div class="mt-12">

        <x-product-carousel
            :products="$alsoOrdered"
            title="Customers Also Bought"
            subtitle="Frequently ordered together"
            id="carousel-also"
        />

    </div>

@endif


{{-- ═══ RELATED PRODUCTS ═══ --}}
@if($related->isNotEmpty())

    <div class="mt-10">

        <x-section-header
            title="More from {{ $product->category?->name ?? 'This Category' }}"
            :link="route(
                'shop.products.index',
                ['category' => $product->category?->slug]
            )"
        />

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">

            @foreach($related->take(4) as $i => $rel)

                <div data-aos
                     style="animation-delay:{{ $i * 0.07 }}s">

                    <x-product-card :product="$rel" />

                </div>

            @endforeach

        </div>
    </div>

@endif


{{-- ═══ RECENTLY VIEWED ═══ --}}
@if($recentlyViewed->isNotEmpty())

    <div class="mt-10">

        <x-product-carousel
            :products="$recentlyViewed"
            title="Recently Viewed"
            id="carousel-recent"
        />

    </div>

@endif

{{-- ── Mobile sticky add-to-cart bar ──────────────────── --}}
@unless($product->isOutOfStock())
    <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md
                border-t border-ink-100 px-4 py-3 shadow-2xl"
         x-data="{ qty: 1 }">
        <div class="flex items-center gap-3 max-w-md mx-auto">
            <div class="flex items-center border border-ink-200 rounded-xl overflow-hidden flex-shrink-0">
                <button @click="qty = Math.max(1, qty - 1)"
                        class="w-9 h-9 flex items-center justify-center text-ink-600
                               hover:bg-ink-50 transition-colors font-medium">
                    −
                </button>
                <span class="w-8 text-center text-sm font-semibold" x-text="qty"></span>
                <button @click="qty = Math.min({{ $product->stock }}, qty + 1)"
                        class="w-9 h-9 flex items-center justify-center text-ink-600
                               hover:bg-ink-50 transition-colors font-medium">
                    +
                </button>
            </div>
            <form method="POST" action="{{ route('cart.store') }}" class="flex-1">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" :value="qty">
                <button type="submit"
                        class="btn-primary w-full justify-center py-2.5 text-sm rounded-xl">
                    Add to Cart · ₹{{ number_format($product->price, 0) }}
                </button>
            </form>
        </div>
    </div>
    <div class="lg:hidden h-20"></div>{{-- Spacer to prevent content hiding behind bar --}}
@endunless


@endsection