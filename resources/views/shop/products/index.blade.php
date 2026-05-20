@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="productFilter()">

    {{-- ── Breadcrumb ─────────────────────────────────── --}}
    <nav class="flex items-center gap-2 text-xs text-ink-400 mb-6">
        <a href="{{ url('/') }}" class="hover:text-ink-700 transition-colors">Home</a>
        <span>/</span>
        <span class="text-ink-700 font-medium">Products</span>
        @if(request('category'))
            <span>/</span>
            <span class="text-ink-700 font-medium capitalize">{{ request('category') }}</span>
        @endif
    </nav>

    {{-- Did-you-mean --}}
@if(request('q') && $didYouMean = app(\App\Services\SearchService::class)->didYouMean(request('q')))
    <div class="mb-4 text-sm text-ink-600">
        Did you mean:
        <a href="{{ request()->fullUrlWithQuery(['q' => $didYouMean]) }}"
           class="text-brand-600 font-semibold hover:underline">
            {{ $didYouMean }}
        </a>?
    </div>
@endif

{{-- Trending keywords chips (show when no search active) --}}
@if(!request('q'))
    @php $keywords = app(\App\Services\SearchService::class)->trendingKeywords(8); @endphp
    <div class="flex flex-wrap gap-2 mb-5">
        <span class="text-xs text-ink-400 font-medium self-center">🔥 Trending:</span>
        @foreach($keywords as $kw)
            <a href="{{ route('shop.products.index', ['q' => $kw]) }}"
               class="inline-flex items-center px-3 py-1.5 bg-white border border-ink-200
                      rounded-full text-xs font-medium text-ink-700
                      hover:border-brand-400 hover:text-brand-700
                      hover:bg-brand-50 transition-all">
                {{ $kw }}
            </a>
        @endforeach
    </div>
@endif

    <div class="flex gap-7 items-start">

        {{-- ════════════════════════════════════════════════
             SIDEBAR FILTERS — desktop
        ════════════════════════════════════════════════ --}}
        <aside class="hidden lg:block w-60 flex-shrink-0 sticky top-24">
            <div class="card p-5 space-y-6">

                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-ink-900 text-sm">Filters</h2>
                    @if(request()->hasAny(['q','category','min_price','max_price','min_rating','in_stock']))
                        <a href="{{ route('shop.products.index') }}"
                           class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                            Clear all
                        </a>
                    @endif
                </div>

                <form method="GET" action="{{ route('shop.products.index') }}" id="filter-form">

                    {{-- Search --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="Product name…"
                                   class="input text-sm pr-8">
                            <svg class="absolute right-2.5 top-2.5 w-4 h-4 text-ink-300 pointer-events-none"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Category --}}
                    @if($categories->isNotEmpty())
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">
                            Category
                        </label>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value=""
                                       {{ !request('category') ? 'checked' : '' }}
                                       class="accent-brand-600"
                                       onchange="this.form.submit()">
                                <span class="text-sm text-ink-700 group-hover:text-ink-900">All</span>
                            </label>
                            @foreach($categories as $cat)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat->slug }}"
                                           {{ request('category') === $cat->slug ? 'checked' : '' }}
                                           class="accent-brand-600"
                                           onchange="this.form.submit()">
                                    <span class="text-sm text-ink-700 group-hover:text-ink-900">
                                        {{ $cat->name }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Price Range --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">
                            Price Range
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="₹ Min" min="0"
                                   class="input text-sm w-full">
                            <span class="text-ink-300 flex-shrink-0">—</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="₹ Max" min="0"
                                   class="input text-sm w-full">
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-ink-500 uppercase tracking-wider mb-2">
                            Min Rating
                        </label>
                        <div class="flex gap-1.5">
                            @foreach([4,3,2,1] as $r)
                                <button type="button"
                                        onclick="document.querySelector('[name=min_rating]').value='{{ $r }}'; document.getElementById('filter-form').submit()"
                                        class="flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-medium
                                               transition-all {{ request('min_rating') == $r
                                                   ? 'bg-brand-600 border-brand-600 text-white'
                                                   : 'border-ink-200 text-ink-600 hover:border-brand-400' }}">
                                    {{ $r }}★+
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="min_rating" value="{{ request('min_rating') }}">
                    </div>

                    {{-- In stock --}}
                    <div class="mb-5">
                        <label class="flex items-center gap-2.5 cursor-pointer">
                            <input type="checkbox" name="in_stock" value="1"
                                   {{ request('in_stock') ? 'checked' : '' }}
                                   class="w-4 h-4 rounded accent-brand-600"
                                   onchange="this.form.submit()">
                            <span class="text-sm text-ink-700">In stock only</span>
                        </label>
                    </div>

                    {{-- Keep sort value --}}
                    <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

                    <button type="submit" class="btn-primary w-full justify-center text-sm">
                        Apply Filters
                    </button>
                </form>
            </div>
        </aside>

        {{-- ════════════════════════════════════════════════
             MAIN CONTENT
        ════════════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0">

            {{-- Toolbar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                <div class="flex items-center gap-3">
                    {{-- Mobile filter toggle --}}
                    <button @click="filterOpen = true"
                            class="lg:hidden btn-secondary text-sm gap-2 py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filters
                    </button>
                    <p class="text-sm text-ink-500">
                        <span class="font-semibold text-ink-900">{{ $products->total() }}</span>
                        product{{ $products->total() !== 1 ? 's' : '' }}
                    </p>
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-2">
                    <span class="text-xs text-ink-400 hidden sm:block">Sort:</span>
                    <form method="GET" action="{{ route('shop.products.index') }}" id="sort-form">
                        @foreach(request()->except('sort') as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <select name="sort"
                                onchange="document.getElementById('sort-form').submit()"
                                class="input text-sm py-2 pr-8 w-auto cursor-pointer">
                            <option value="newest"     {{ request('sort','newest') === 'newest'     ? 'selected' : '' }}>Newest</option>
                            <option value="popular"    {{ request('sort') === 'popular'             ? 'selected' : '' }}>Most Popular</option>
                            <option value="rating"     {{ request('sort') === 'rating'              ? 'selected' : '' }}>Top Rated</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'           ? 'selected' : '' }}>Price: Low → High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc'          ? 'selected' : '' }}>Price: High → Low</option>
                        </select>
                    </form>
                </div>
            </div>

            {{-- Active filter chips --}}
            @php
                $active = array_filter([
                    'q'          => request('q'),
                    'category'   => request('category'),
                    'min_price'  => request('min_price') ? '₹'.request('min_price').' min' : null,
                    'max_price'  => request('max_price') ? '₹'.request('max_price').' max' : null,
                    'min_rating' => request('min_rating') ? request('min_rating').'★+' : null,
                    'in_stock'   => request('in_stock') ? 'In stock' : null,
                ]);
            @endphp
            @if(count($active))
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($active as $key => $label)
                        <a href="{{ request()->fullUrlWithoutQuery([$key]) }}"
                           class="inline-flex items-center gap-1.5 bg-brand-50 border border-brand-200
                                  text-brand-700 rounded-full px-3 py-1 text-xs font-medium
                                  hover:bg-brand-100 transition-colors">
                            {{ $label }}
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Grid --}}
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <div class="w-20 h-20 bg-ink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-9 h-9 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-ink-700 text-lg">No products found</p>
                    <p class="text-ink-400 text-sm mt-1">Try adjusting your filters.</p>
                    <a href="{{ route('shop.products.index') }}" class="btn-primary mt-5 inline-flex">
                        Clear Filters
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($products as $i => $product)
                        <div data-aos style="animation-delay:{{ ($i % 8) * 0.05 }}s">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="mt-8 flex justify-center">
                        {{ $products->onEachSide(1)->links('components.pagination') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ── Mobile filter drawer ──────────────────────────── --}}
<div x-show="filterOpen"
     class="fixed inset-0 z-50 lg:hidden"
     x-cloak>
    <div class="absolute inset-0 bg-ink-900/60 backdrop-blur-sm"
         @click="filterOpen = false">
    </div>
    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl
                max-h-[88vh] overflow-y-auto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="translate-y-full">

        <div class="sticky top-0 bg-white px-5 pt-4 pb-3 border-b border-ink-100 flex items-center justify-between">
            <h3 class="font-semibold text-ink-900">Filters</h3>
            <button @click="filterOpen = false" class="btn-ghost p-1.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-5">
            {{-- Reuse desktop form content --}}
            <form method="GET" action="{{ route('shop.products.index') }}">

                {{-- Category --}}
                @if($categories->isNotEmpty())
                <div class="mb-6">
                    <p class="text-xs font-semibold text-ink-400 uppercase tracking-wider mb-3">Category</p>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ request()->fullUrlWithoutQuery(['category']) }}"
                           class="px-4 py-2 rounded-full text-sm border transition-colors
                                  {{ !request('category') ? 'bg-brand-600 text-white border-brand-600' : 'border-ink-200 text-ink-600' }}">
                            All
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug]) }}"
                               class="px-4 py-2 rounded-full text-sm border transition-colors
                                      {{ request('category') === $cat->slug ? 'bg-brand-600 text-white border-brand-600' : 'border-ink-200 text-ink-600' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Price --}}
                <div class="mb-6">
                    <p class="text-xs font-semibold text-ink-400 uppercase tracking-wider mb-3">Price</p>
                    <div class="flex gap-3">
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                               placeholder="Min ₹" class="input text-sm flex-1">
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="Max ₹" class="input text-sm flex-1">
                    </div>
                </div>

                {{-- In stock --}}
                <label class="flex items-center gap-3 cursor-pointer mb-6">
                    <input type="checkbox" name="in_stock" value="1"
                           {{ request('in_stock') ? 'checked' : '' }}
                           class="w-5 h-5 rounded accent-brand-600">
                    <span class="text-sm text-ink-700 font-medium">In stock only</span>
                </label>

                <input type="hidden" name="sort" value="{{ request('sort', 'newest') }}">

                <button type="submit" @click="filterOpen = false"
                        class="btn-primary w-full justify-center">
                    Apply Filters
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function productFilter() {
    return { filterOpen: false }
}
</script>
@endpush
@endsection