@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Shop the latest trends. Free delivery on orders above ₹999.')

@section('content')

{{-- ══════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-ink-900 min-h-[520px] sm:min-h-[600px] flex items-center">

    {{-- Geometric background texture --}}
    <div class="absolute inset-0 pointer-events-none select-none" aria-hidden="true">
        {{-- Warm gradient mesh --}}
        <div class="absolute inset-0"
             style="background: radial-gradient(ellipse 80% 60% at 60% 110%, rgba(217,119,6,0.35) 0%, transparent 70%),
                                radial-gradient(ellipse 50% 50% at 90% 20%,  rgba(245,158,11,0.15) 0%, transparent 60%),
                                linear-gradient(135deg, #1a1612 0%, #2e2820 50%, #1a1612 100%);">
        </div>
        {{-- Dot grid overlay --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: radial-gradient(circle, #fff 1px, transparent 1px);
                    background-size: 32px 32px;">
        </div>
        {{-- Diagonal accent line --}}
        <div class="absolute -right-20 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-brand-500/30 to-transparent"
             style="transform: rotate(12deg) translateX(-120px);"></div>
        <div class="absolute -right-20 top-0 bottom-0 w-px bg-gradient-to-b from-transparent via-brand-400/20 to-transparent"
             style="transform: rotate(12deg) translateX(-80px);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-24 w-full">
        <div class="max-w-2xl">

            {{-- Pill tag --}}
            <div class="inline-flex items-center gap-2 bg-brand-500/20 border border-brand-500/30
                        rounded-full px-4 py-1.5 mb-6 animate-fade-in">
                <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-pulse"></span>
                <span class="text-brand-300 text-xs font-semibold tracking-wider uppercase">
                    New Season Arrivals
                </span>
            </div>

            {{-- Headline --}}
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl text-white leading-[1.1]
                       tracking-tight animate-fade-in-up" style="animation-delay:0.1s">
                Discover <em class="not-italic text-brand-400">Premium</em><br>
                Quality Goods
            </h1>

            <p class="mt-5 text-ink-300 text-base sm:text-lg leading-relaxed max-w-lg
                      animate-fade-in-up" style="animation-delay:0.2s">
                Handpicked products, honest prices, and the convenience of fast delivery
                straight to your door.
            </p>

            {{-- CTAs --}}
            <div class="flex flex-wrap gap-3 mt-8 animate-fade-in-up" style="animation-delay:0.3s">
                <a href="{{ route('shop.products.index') }}"
                   class="btn-primary text-base px-7 py-3 rounded-xl shadow-lg shadow-brand-900/30">
                    Shop Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('shop.products.index', ['featured' => 1]) }}"
                   class="btn-secondary bg-white/10 border-white/20 text-white hover:bg-white/20
                          text-base px-7 py-3 rounded-xl">
                    Featured Picks
                </a>
            </div>

            {{-- Mini trust strip --}}
            <div class="flex flex-wrap gap-5 mt-10 animate-fade-in-up" style="animation-delay:0.4s">
                @foreach ([
                    ['val' => '10K+',    'label' => 'Happy Customers'],
                    ['val' => '500+',    'label' => 'Products'],
                    ['val' => '₹0',      'label' => 'Delivery on ₹999+'],
                    ['val' => '4.8★',    'label' => 'Avg Rating'],
                ] as $stat)
                    <div>
                        <p class="text-white font-bold text-lg leading-none">{{ $stat['val'] }}</p>
                        <p class="text-ink-400 text-xs mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Scroll hint --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5
                animate-bounce hidden sm:flex">
        <span class="text-ink-500 text-[10px] uppercase tracking-widest">Scroll</span>
        <div class="w-px h-8 bg-gradient-to-b from-ink-500 to-transparent"></div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     FEATURED CATEGORIES
══════════════════════════════════════════════════════ --}}
@if ($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 py-14 sm:py-16">

    <x-section-header
        title="Shop by Category"
        subtitle="Browse our curated collections"
    />

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-{{ min($categories->count(), 6) }} gap-3 sm:gap-4">
        @foreach ($categories->take(6) as $i => $category)
            <a href="{{ route('shop.products.index', ['category' => $category->slug]) }}"
               class="group relative overflow-hidden rounded-2xl bg-ink-900
                      aspect-[4/3] sm:aspect-square flex items-end p-4
                      transition-all duration-300 hover:scale-[1.02] hover:shadow-card-hover"
               data-aos style="animation-delay: {{ $i * 0.07 }}s">

                {{-- Gradient overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-ink-900/90 via-ink-900/30 to-transparent
                             group-hover:from-brand-900/90 group-hover:via-brand-900/30 transition-all duration-400">
                </div>

                {{-- Placeholder visual (replace with category image when available) --}}
                @php
                    $palettes = [
                        'from-amber-800 to-amber-600',
                        'from-stone-700 to-stone-500',
                        'from-orange-800 to-orange-600',
                        'from-yellow-700 to-yellow-500',
                        'from-red-800 to-red-600',
                        'from-zinc-700 to-zinc-500',
                    ];
                @endphp
                <div class="absolute inset-0 bg-gradient-to-br {{ $palettes[$loop->index % count($palettes)] }}
                             opacity-60 group-hover:opacity-70 transition-opacity">
                </div>

                {{-- Category icon backdrop --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-10">
                    <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/>
                    </svg>
                </div>

                {{-- Label --}}
                <div class="relative">
                    <p class="text-white font-semibold text-sm sm:text-base leading-tight">
                        {{ $category->name }}
                    </p>
                    @if ($category->products_count ?? 0 > 0)
                        <p class="text-white/60 text-xs mt-0.5">
                            {{ $category->products_count }} items
                        </p>
                    @endif
                    <div class="mt-2 flex items-center gap-1 text-brand-300 text-xs font-medium
                                opacity-0 group-hover:opacity-100 transition-opacity duration-200 -translate-x-1
                                group-hover:translate-x-0">
                        Shop now
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════════════
     FEATURED PRODUCTS
══════════════════════════════════════════════════════ --}}
@if ($featuredProducts->isNotEmpty())
<section class="bg-ink-50 py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-4">

        <x-section-header
            title="Featured Products"
            subtitle="Handpicked for quality and value"
            :link="route('shop.products.index', ['featured' => 1])"
        />

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
            @foreach ($featuredProducts as $i => $product)
                <div data-aos style="animation-delay: {{ $i * 0.06 }}s">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════════════
     PROMO BANNER STRIP
══════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-10 sm:py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- Banner 1 — Sale --}}
        <div class="relative overflow-hidden rounded-2xl bg-brand-600 p-6 sm:p-8 flex flex-col
                    justify-between min-h-[180px] group" data-aos>
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full"></div>
                <div class="absolute -right-4 -bottom-12 w-56 h-56 bg-white/5 rounded-full"></div>
                <div class="absolute inset-0 opacity-5"
                     style="background-image: radial-gradient(circle, #fff 1px, transparent 1px);
                            background-size: 24px 24px;"></div>
            </div>
            <div class="relative">
                <span class="badge bg-white/20 text-white text-xs px-3 py-1 mb-3 inline-block">
                    Limited Time
                </span>
                <h3 class="font-display text-2xl sm:text-3xl text-white leading-tight">
                    Season Sale<br><em class="not-italic text-brand-200">Up to 40% Off</em>
                </h3>
            </div>
            <a href="{{ route('shop.products.index', ['sort' => 'price_asc']) }}"
               class="relative inline-flex items-center gap-2 bg-white text-brand-700
                      font-semibold text-sm px-5 py-2.5 rounded-xl w-fit mt-4
                      hover:bg-brand-50 transition-colors group-hover:shadow-md">
                Shop Sale
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Banner 2 — Free Shipping --}}
        <div class="relative overflow-hidden rounded-2xl bg-ink-900 p-6 sm:p-8 flex flex-col
                    justify-between min-h-[180px] group" data-aos style="animation-delay:0.1s">
            <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
                <div class="absolute -left-8 -bottom-8 w-40 h-40 bg-brand-500/20 rounded-full"></div>
                <div class="absolute right-6 top-6 w-24 h-24 bg-brand-500/10 rounded-full"></div>
            </div>
            <div class="relative">
                <span class="badge bg-brand-500/20 text-brand-300 text-xs px-3 py-1 mb-3 inline-block">
                    Always Free
                </span>
                <h3 class="font-display text-2xl sm:text-3xl text-white leading-tight">
                    Free Delivery<br>
                    <em class="not-italic text-brand-400">On ₹999+ Orders</em>
                </h3>
            </div>
            <a href="{{ route('shop.products.index') }}"
               class="relative inline-flex items-center gap-2 bg-brand-600 text-white
                      font-semibold text-sm px-5 py-2.5 rounded-xl w-fit mt-4
                      hover:bg-brand-700 transition-colors">
                Explore Now
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════════════
     NEW ARRIVALS
══════════════════════════════════════════════════════ --}}
@if ($latestProducts->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 pb-14 sm:pb-16">

    <x-section-header
        title="New Arrivals"
        subtitle="Just dropped — fresh picks this week"
        :link="route('shop.products.index', ['sort' => 'newest'])"
    />

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 lg:gap-5">
        @foreach ($latestProducts as $i => $product)
            <div data-aos style="animation-delay: {{ $i * 0.05 }}s">
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════════════
     TOP RATED STRIP
══════════════════════════════════════════════════════ --}}
@if ($topRated->isNotEmpty())
<section class="bg-gradient-to-br from-ink-900 to-ink-800 py-14 sm:py-16">
    <div class="max-w-7xl mx-auto px-4">

        <x-section-header
            title="Top Rated"
            subtitle="Loved by thousands of customers"
            :link="route('shop.products.index', ['sort' => 'rating'])"
        />

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach ($topRated as $i => $product)
                @php $image = $product->primaryImage?->url ?? null; @endphp
                <a href="{{ route('shop.products.show', $product) }}"
                   class="group relative overflow-hidden rounded-2xl bg-white/5 border border-white/10
                          hover:border-brand-500/50 p-4 flex flex-col gap-3 transition-all duration-300
                          hover:bg-white/10"
                   data-aos style="animation-delay: {{ $i * 0.08 }}s">

                    {{-- Image --}}
                    <div class="aspect-square rounded-xl overflow-hidden bg-ink-700">
                        @if ($image)
                            <img src="{{ $image }}" alt="{{ $product->name }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-ink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div>
                        <p class="text-white text-sm font-medium line-clamp-2 leading-snug">
                            {{ $product->name }}
                        </p>
                        {{-- Stars --}}
                        <div class="flex items-center gap-1 mt-1.5">
                            @for ($s = 1; $s <= 5; $s++)
                                <svg class="w-3 h-3 {{ $s <= round($product->avg_rating) ? 'text-brand-400' : 'text-white/20' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="text-white/40 text-[10px] ml-1">
                                {{ number_format($product->avg_rating, 1) }}
                            </span>
                        </div>
                        <p class="text-brand-400 font-bold text-sm mt-1.5">
                            ₹{{ number_format($product->price, 0) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════════════
     WHY US / FEATURES STRIP
══════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-12 sm:py-16">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        @foreach ([
            ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
             'title' => 'Free Shipping', 'sub' => 'On orders above ₹999'],
            ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
             'title' => 'Easy Returns',  'sub' => '30-day hassle-free returns'],
            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
             'title' => 'Secure Pay',    'sub' => 'Protected by 256-bit SSL'],
            ['icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
             'title' => '24/7 Support',  'sub' => 'We\'re always here for you'],
        ] as $i => $feat)
            <div class="card p-5 text-center group hover:-translate-y-1 transition-transform duration-200"
                 data-aos style="animation-delay: {{ $i * 0.08 }}s">
                <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center
                            mx-auto mb-3 group-hover:bg-brand-100 transition-colors">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor"
                         stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                    </svg>
                </div>
                <p class="font-semibold text-ink-900 text-sm">{{ $feat['title'] }}</p>
                <p class="text-ink-500 text-xs mt-1 leading-relaxed">{{ $feat['sub'] }}</p>
            </div>
        @endforeach
    </div>
</section>

@endsection