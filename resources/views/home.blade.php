@extends('layouts.app')
@section('title', 'Home')

@section('content')

{{-- ═══════════════════ HERO ═══════════════════ --}}
<section class="relative overflow-hidden bg-ink-900 min-h-[520px] sm:min-h-[580px]
                flex items-center">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0"
             style="background:radial-gradient(ellipse 80% 60% at 60% 110%,rgba(217,119,6,.35) 0%,transparent 70%),
                    radial-gradient(ellipse 50% 50% at 90% 20%,rgba(245,158,11,.15) 0%,transparent 60%),
                    linear-gradient(135deg,#1a1612 0%,#2e2820 50%,#1a1612 100%)">
        </div>
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:32px 32px">
        </div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-20 w-full">
        <div class="max-w-xl">
            <div class="inline-flex items-center gap-2 bg-brand-500/20 border border-brand-500/30
                        rounded-full px-4 py-1.5 mb-5 animate-fade-in">
                <span class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-pulse"></span>
                <span class="text-brand-300 text-xs font-semibold tracking-wider uppercase">
                    New Season Arrivals
                </span>
            </div>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl text-white leading-[1.1]
                       tracking-tight animate-fade-in-up" style="animation-delay:.1s">
                Discover <em class="not-italic text-brand-400">Premium</em><br>
                Quality Goods
            </h1>
            <p class="mt-4 text-ink-300 text-base leading-relaxed max-w-sm
                      animate-fade-in-up" style="animation-delay:.2s">
                {{ config('brand.description') }}
            </p>
            <div class="flex flex-wrap gap-3 mt-7 animate-fade-in-up" style="animation-delay:.3s">
                <a href="{{ route('shop.products.index') }}"
                   class="btn-primary text-base px-7 py-3 rounded-xl
                          shadow-lg shadow-brand-900/30">
                    Shop Now
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ route('shop.products.index', ['featured' => 1]) }}"
                   class="btn-secondary bg-white/10 border-white/20 text-white
                          hover:bg-white/20 text-base px-7 py-3 rounded-xl">
                    Featured Picks
                </a>
            </div>
            {{-- Live stats --}}
            <div class="flex flex-wrap gap-6 mt-9 animate-fade-in-up" style="animation-delay:.4s">
                @foreach([['val'=>'10K+','label'=>'Happy Customers'],['val'=>'500+','label'=>'Products'],['val'=>'Free','label'=>'Delivery ₹999+'],['val'=>'4.8★','label'=>'Avg Rating']] as $stat)
                    <div>
                        <p class="text-white font-bold text-lg leading-none">{{ $stat['val'] }}</p>
                        <p class="text-ink-400 text-xs mt-0.5">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════ FEATURED COLLECTIONS ═══════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-10 sm:py-12">
    <x-section-header title="Collections" subtitle="Curated just for you" />
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach($featuredCollections as $i => $col)
            @php
                $params = ['sort' => $col['sort']];
                if ($col['tag'])  $params['tag'] = $col['tag'];
                if (isset($col['max_price'])) $params['max_price'] = $col['max_price'];
            @endphp
            <a href="{{ route('shop.products.index', $params) }}"
               class="relative overflow-hidden rounded-2xl min-h-[100px] sm:min-h-[120px]
                      flex items-center gap-4 px-5 py-5 group
                      bg-gradient-to-br {{ $col['color'] }}
                      hover:shadow-lg transition-all duration-300 hover:scale-[1.02]"
               data-aos style="animation-delay:{{ $i * 0.07 }}s">
                <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100
                             transition-opacity duration-300">
                </div>
                <span class="text-3xl flex-shrink-0">{{ $col['icon'] }}</span>
                <div class="relative">
                    <p class="font-bold text-white text-base leading-tight">{{ $col['title'] }}</p>
                    <p class="text-white/70 text-xs mt-0.5 flex items-center gap-1">
                        Shop now
                        <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</section>

{{-- ═══════════════════ FLASH SALE ═══════════════════ --}}
@if($flashSale->isNotEmpty())
<div class="max-w-7xl mx-auto px-4 mb-6">
    <x-flash-sale :products="$flashSale" />
</div>
@endif

{{-- Trending search chips --}}
@if($trendingKeywords)
<div class="max-w-7xl mx-auto px-4 pb-2">
    <div class="flex flex-wrap items-center gap-2">
        <span class="text-xs text-ink-500 font-medium">🔥 Trending:</span>
        @foreach($trendingKeywords as $kw)
            <a href="{{ route('shop.products.index', ['q' => $kw]) }}"
               class="px-3 py-1.5 bg-white border border-ink-200 rounded-full text-xs
                      font-medium text-ink-700 hover:border-brand-400 hover:text-brand-700
                      hover:bg-brand-50 transition-all shadow-sm">
                {{ $kw }}
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- Personalised section --}}
<div class="max-w-7xl mx-auto px-4 py-4">
    <x-product-carousel
        :products="$forYou['products']"
        :title="$forYou['title']"
        :subtitle="$forYou['subtitle']"
        :link="route('shop.products.index')"
        id="carousel-for-you"
        badge="FOR YOU"
        badge-color="purple"
    />
</div>

{{-- ═══════════════════ CATEGORIES ═══════════════════ --}}
@if($categories->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 py-4 sm:py-8">
    <x-section-header title="Shop by Category"
                      subtitle="Browse our curated collections" />
    <div class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-3">
        @foreach($categories->take(6) as $i => $cat)
            <a href="{{ route('shop.products.index', ['category' => $cat->slug]) }}"
               class="group flex flex-col items-center gap-2.5 p-3 sm:p-4
                      rounded-2xl border border-ink-100 bg-white
                      hover:border-brand-300 hover:shadow-card-hover
                      transition-all duration-200 text-center"
               data-aos style="animation-delay:{{ $i * 0.06 }}s">
                @php
                    $catIcons = ['Fashion'=>'👗','Electronics'=>'📱','Home & Living'=>'🏠','Beauty & Health'=>'💄','Sports & Fitness'=>'💪','Books & Media'=>'📚'];
                    $icon = $catIcons[$cat->name] ?? '🛍️';
                @endphp
                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-brand-50
                             group-hover:bg-brand-100 transition-colors flex items-center
                             justify-center text-2xl sm:text-3xl">
                    {{ $icon }}
                </div>
                <p class="text-xs sm:text-sm font-semibold text-ink-800
                           group-hover:text-brand-700 transition-colors leading-tight">
                    {{ $cat->name }}
                </p>
                @if(isset($cat->products_count))
                    <p class="text-[10px] text-ink-400">{{ $cat->products_count }}</p>
                @endif
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════ TRENDING CAROUSEL ═══════════════════ --}}
<div class="max-w-7xl mx-auto px-4 py-4">
    <x-product-carousel
        :products="$trending"
        title="Trending Now"
        subtitle="What everyone's buying"
        :link="route('shop.products.index', ['sort' => 'popular'])"
        badge="HOT"
        id="carousel-trending"
    />
</div>

{{-- ═══════════════════ POPULAR BRANDS ═══════════════════ --}}
<div class="max-w-7xl mx-auto px-4 py-6">
    <x-popular-brands :brands="$popularBrands" />
</div>

{{-- ═══════════════════ FEATURED PRODUCTS GRID ═══════════════════ --}}
@if($featuredProducts->isNotEmpty())
<section class="bg-ink-50 py-10 sm:py-14">
    <div class="max-w-7xl mx-auto px-4">
        <x-section-header
            title="Featured Products"
            subtitle="Handpicked for quality and value"
            :link="route('shop.products.index', ['featured' => 1])"
        />
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($featuredProducts->take(8) as $i => $product)
                <div data-aos style="animation-delay:{{ $i * 0.05 }}s">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════ PROMO BANNERS ═══════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-8">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="relative overflow-hidden rounded-2xl bg-brand-600 p-7 flex flex-col
                    justify-between min-h-[160px] group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full"></div>
            <div class="absolute right-4 bottom-0 w-20 h-20 bg-white/5 rounded-full"></div>
            <div class="relative">
                <span class="badge bg-white/25 text-white text-xs px-2.5 py-1 mb-2 inline-block">
                    Limited Time
                </span>
                <h3 class="font-display text-2xl sm:text-3xl text-white">
                    Season Sale<br>
                    <em class="not-italic text-brand-200">Up to 40% Off</em>
                </h3>
            </div>
            <a href="{{ route('shop.products.index', ['sort' => 'price_asc']) }}"
               class="relative mt-4 inline-flex items-center gap-2 bg-white text-brand-700
                      font-semibold text-sm px-5 py-2.5 rounded-xl w-fit
                      hover:bg-brand-50 transition-colors">
                Shop Sale →
            </a>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-ink-900 p-7 flex flex-col
                    justify-between min-h-[160px] group">
            <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-brand-500/20 rounded-full"></div>
            <div class="relative">
                <span class="badge bg-brand-500/25 text-brand-300 text-xs px-2.5 py-1
                             mb-2 inline-block">
                    Always Free
                </span>
                <h3 class="font-display text-2xl sm:text-3xl text-white">
                    Free Delivery<br>
                    <em class="not-italic text-brand-400">On ₹999+ Orders</em>
                </h3>
            </div>
            <a href="{{ route('shop.products.index') }}"
               class="relative mt-4 inline-flex items-center gap-2 bg-brand-600 text-white
                      font-semibold text-sm px-5 py-2.5 rounded-xl w-fit
                      hover:bg-brand-700 transition-colors">
                Explore Now →
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════ NEW ARRIVALS CAROUSEL ═══════════════════ --}}
<div class="max-w-7xl mx-auto px-4 pb-8">
    <x-product-carousel
        :products="$latestProducts"
        title="New Arrivals"
        subtitle="Just dropped this week"
        :link="route('shop.products.index', ['sort' => 'newest'])"
        badge="NEW"
        badge-color="blue"
        id="carousel-new"
    />
</div>

{{-- ═══════════════════ FEATURES STRIP ═══════════════════ --}}
<section class="max-w-7xl mx-auto px-4 py-10 border-t border-ink-100">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['icon'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10','title'=>'Free Shipping','sub'=>'On orders above ₹999'],
            ['icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15','title'=>'Easy Returns','sub'=>'30-day hassle-free'],
            ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Secure Pay','sub'=>'256-bit SSL'],
            ['icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z','title'=>'24/7 Support','sub'=>'Always here for you'],
        ] as $f)
            <div class="card p-4 sm:p-5 text-center group hover:-translate-y-0.5 transition-transform">
                <div class="w-11 h-11 rounded-xl bg-brand-50 group-hover:bg-brand-100
                             transition-colors flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor"
                         stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <p class="font-semibold text-ink-900 text-sm">{{ $f['title'] }}</p>
                <p class="text-ink-400 text-xs mt-0.5">{{ $f['sub'] }}</p>
            </div>
        @endforeach
    </div>
</section>

@endsection