<header class="sticky top-0 z-50 nav-glass" x-data="navbar()">

    {{-- ── Top bar ─────────────────────────────────────── --}}
    <div class="hidden sm:block bg-ink-900">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-8">
            <p class="text-ink-400 text-[11px] tracking-wide">
                🚚 Free delivery on orders above
                <span class="text-brand-400 font-semibold">
                    ₹{{ config('brand.free_shipping_threshold') }}
                </span>
            </p>
            <div class="flex items-center gap-4 text-[11px] text-ink-400">
                <span>📞 {{ config('brand.phone') }}</span>
                <a href="mailto:{{ config('brand.support') }}"
                   class="hover:text-brand-400 transition-colors">
                    {{ config('brand.support') }}
                </a>
            </div>
        </div>
    </div>

    {{-- ── Main nav ─────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center h-16 gap-4">

            {{-- Mobile menu button --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden btn-ghost p-2 -ml-2 flex-shrink-0">
                <span class="sr-only">Menu</span>
                <div class="w-5 flex flex-col gap-1.5 transition-all"
                     :class="mobileOpen ? 'gap-0' : ''">
                    <span class="block h-0.5 bg-ink-700 rounded transition-all"
                          :class="mobileOpen ? 'rotate-45 translate-y-0.5' : ''"></span>
                    <span class="block h-0.5 bg-ink-700 rounded transition-all"
                          :class="mobileOpen ? 'opacity-0 w-0' : 'w-full'"></span>
                    <span class="block h-0.5 bg-ink-700 rounded transition-all"
                          :class="mobileOpen ? '-rotate-45 -translate-y-1' : ''"></span>
                </div>
            </button>

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex-shrink-0 group">
                <x-logo size="md" />
            </a>

            {{-- Search -- desktop --}}
            <div class="flex-1 max-w-lg hidden md:block" x-data="searchBar()">
                <div class="relative">
                    <div class="absolute inset-y-0 left-3.5 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="search()"
                           @focus="open = true"
                           @click.outside="open = false"
                           @keydown.escape="open = false"
                           placeholder="Search {{ config('brand.name') }}…"
                           class="w-full pl-10 pr-4 py-2.5 bg-ink-50 border border-ink-200
                                  rounded-xl text-sm text-ink-900 placeholder:text-ink-400
                                  focus:outline-none focus:ring-2 focus:ring-brand-400/50
                                  focus:border-brand-400 focus:bg-white transition-all duration-200">

                    {{-- Autocomplete dropdown --}}
                    <div x-show="open && results.length > 0"
     @click.outside="open = false"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 scale-98 -translate-y-1"
     class="absolute top-full left-0 right-0 mt-2 bg-white border border-ink-100
            rounded-2xl shadow-xl overflow-hidden z-50">

    {{-- Label --}}
    <div class="px-4 py-2 border-b border-ink-50 flex items-center justify-between">

        <p class="text-[11px] font-semibold uppercase tracking-widest text-ink-400">

            <span x-show="isTrending">
                🔥 Trending
            </span>

            <span x-show="!isTrending">
                Results
            </span>
        </p>

        <div x-show="loading"
             class="w-3 h-3 border-2 border-brand-500 border-t-transparent
                    rounded-full animate-spin">
        </div>
    </div>

    <template x-for="item in results" :key="item.uuid">

        <a :href="'/shop/products/' + item.uuid"
           class="flex items-center justify-between px-4 py-2.5
                  hover:bg-ink-50 transition-colors group
                  border-b border-ink-50 last:border-0">

            <div class="flex items-center gap-2.5 min-w-0">

                <span class="w-1.5 h-1.5 bg-brand-400 rounded-full
                             flex-shrink-0 opacity-0
                             group-hover:opacity-100 transition-opacity">
                </span>

                <div class="min-w-0">

                    <p class="text-sm text-ink-800 truncate"
                       x-text="item.name">
                    </p>

                    <p class="text-[11px] text-ink-400 truncate"
                       x-text="item.brand || ''">
                    </p>
                </div>
            </div>

            <span class="text-xs font-semibold text-ink-500
                         font-mono flex-shrink-0 ml-4"
                  x-text="'₹' + parseFloat(item.price).toLocaleString('en-IN')">
            </span>
        </a>

    </template>

    <div class="px-4 py-2.5 bg-ink-50/50">

        <button @click="submitSearch()"
                class="text-xs text-brand-600 hover:text-brand-700
                       font-semibold flex items-center gap-1">

            <svg class="w-3 h-3"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2.5"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>

            Search for "
            <span x-text="query || 'trending'"></span>
            "
        </button>
    </div>
</div>
                </div>
            </div>

            {{-- Right cluster --}}
            <div class="flex items-center gap-0.5 ml-auto md:ml-0">

                {{-- Mobile search --}}
                <button @click="searchOpen = !searchOpen"
                        class="md:hidden btn-ghost p-2.5 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </button>

                {{-- Wishlist --}}
                @auth
                    @php $wCount = auth()->user()->wishlist?->totalItems() ?? 0; @endphp
                    <a href="{{ route('wishlist.index') }}"
                       class="btn-ghost p-2.5 relative hidden sm:flex items-center"
                       title="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        @if($wCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white
                                         text-[9px] font-bold rounded-full flex items-center
                                         justify-center leading-none">
                                {{ min($wCount, 9) }}
                            </span>
                        @endif
                    </a>
                @endauth

                {{-- Cart --}}
                @php
                    $cartCount = auth()->check()
                        ? (auth()->user()->cart?->totalItems() ?? 0)
                        : 0;
                @endphp
                {{-- Replace existing cart <a> tag --}}
<button data-open-cart
        class="relative flex items-center gap-2 btn-ghost px-3 py-2.5 ml-0.5"
        title="Cart">
    <div class="relative">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        @if($cartCount > 0)
            <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-brand-600
                         text-white text-[9px] font-bold rounded-full flex
                         items-center justify-center leading-none"
                  data-cart-count="{{ $cartCount }}">
                {{ min($cartCount, 9) }}
            </span>
        @endif
    </div>
    <span class="hidden sm:block text-sm font-medium text-ink-700 leading-none">
        Cart
        @if($cartCount > 0)
            <span class="block text-xs text-brand-600 font-semibold mt-0.5">
                {{ $cartCount }} item{{ $cartCount !== 1 ? 's' : '' }}
            </span>
        @endif
    </span>
</button>
                   class="relative flex items-center gap-2 btn-ghost px-3 py-2.5 ml-0.5"
                   title="Cart">
                    <div class="relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-brand-600
                                         text-white text-[9px] font-bold rounded-full flex
                                         items-center justify-center leading-none">
                                {{ min($cartCount, 9) }}
                            </span>
                        @endif
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-ink-700 leading-none">
                        Cart
                        @if($cartCount > 0)
                            <span class="block text-xs text-brand-600 font-semibold mt-0.5">
                                {{ $cartCount }} item{{ $cartCount !== 1 ? 's' : '' }}
                            </span>
                        @endif
                    </span>
                </a>

                {{-- Divider --}}
                <div class="w-px h-6 bg-ink-200 mx-1 hidden sm:block"></div>

                {{-- Auth --}}
                @auth
                    <div class="relative hidden sm:block" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2.5 btn-ghost pl-2 pr-3 py-2">
                            {{-- Avatar --}}
                            <div class="w-7 h-7 rounded-lg overflow-hidden bg-brand-100
                                        flex items-center justify-center flex-shrink-0">
                                @if(auth()->user()->avatar)
                                    <img src="{{ \Storage::disk('public')->url(auth()->user()->avatar) }}"
                                         alt="{{ auth()->user()->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <span class="text-brand-700 text-xs font-bold">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-sm font-medium text-ink-800 max-w-[96px] truncate
                                         hidden lg:block">
                                {{ auth()->user()->name }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-ink-400 flex-shrink-0 transition-transform duration-200"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl
                                    border border-ink-100 shadow-xl overflow-hidden z-50"
                             x-cloak>

                            {{-- Header --}}
                            <div class="px-4 pt-3.5 pb-3 bg-ink-50 border-b border-ink-100">
                                <p class="text-xs font-bold text-ink-900 truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-[11px] text-ink-400 truncate mt-0.5">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>

                            <div class="py-1.5">
                                @php $links = [
                                    ['href' => route('profile.edit'),   'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',          'label' => 'My Profile'],
                                    ['href' => route('orders.index'),   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'label' => 'My Orders'],
                                    ['href' => route('wishlist.index'), 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'label' => 'Wishlist'],
                                    ['href' => route('addresses.index'),'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'label' => 'Addresses'],
                                ]; @endphp
                                @foreach($links as $link)
                                    <a href="{{ $link['href'] }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm
                                              text-ink-700 hover:bg-ink-50 hover:text-ink-900
                                              transition-colors">
                                        <svg class="w-4 h-4 text-ink-400 flex-shrink-0" fill="none"
                                             stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="{{ $link['icon'] }}"/>
                                        </svg>
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>

                            @if(auth()->user()->isAdmin())
                                <div class="border-t border-ink-100 py-1.5">
                                    <a href="{{ route('admin.analytics.dashboard') }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm
                                              text-brand-700 hover:bg-brand-50 transition-colors font-medium">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                             stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                        </svg>
                                        Admin Panel
                                        <svg class="w-3 h-3 ml-auto text-brand-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            @endif

                            <div class="border-t border-ink-100 py-1.5">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-3 px-4 py-2 w-full text-sm
                                                   text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                             stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('login') }}"
                           class="btn-ghost text-sm px-4 py-2 font-medium">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}"
                           class="btn-primary text-sm px-4 py-2 rounded-xl">
                            Register
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- Mobile search bar --}}
        <div x-show="searchOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden pb-3 -mt-1" x-data="searchBar()" x-cloak>
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </div>
                <input type="text" x-model="query"
                       @input.debounce.300ms="search()"
                       placeholder="Search products…"
                       autofocus
                       class="w-full pl-9 pr-4 py-2.5 bg-ink-50 border border-ink-200
                              rounded-xl text-sm focus:outline-none focus:ring-2
                              focus:ring-brand-400/50 focus:bg-white transition-all">
            </div>
        </div>

        {{-- Category strip --}}
        <nav class="hidden lg:flex items-center gap-0 border-t border-ink-100/70 -mx-4 px-4
                     overflow-x-auto scrollbar-none">
            <a href="{{ route('shop.products.index') }}"
               class="flex-shrink-0 px-4 py-2.5 text-[13px] font-medium transition-colors
                      border-b-2 -mb-px
                      {{ request()->routeIs('shop.products.index') && !request('category')
                          ? 'border-brand-600 text-brand-700'
                          : 'border-transparent text-ink-600 hover:text-ink-900 hover:border-ink-300' }}">
                All
            </a>
            @foreach(app(\App\Services\CacheService::class)->categories() as $cat)
                <a href="{{ route('shop.products.index', ['category' => $cat->slug]) }}"
                   class="flex-shrink-0 px-4 py-2.5 text-[13px] font-medium transition-colors
                          border-b-2 -mb-px whitespace-nowrap
                          {{ request('category') === $cat->slug
                              ? 'border-brand-600 text-brand-700'
                              : 'border-transparent text-ink-600 hover:text-ink-900 hover:border-ink-300' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </nav>
    </div>

    {{-- Mobile drawer --}}
    <div x-show="mobileOpen" class="lg:hidden fixed inset-0 z-50" x-cloak>
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-ink-900/60 backdrop-blur-sm"
             @click="mobileOpen = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0">
        </div>

        {{-- Drawer --}}
        <div class="absolute inset-y-0 left-0 w-72 bg-white flex flex-col shadow-2xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-end="-translate-x-full">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-ink-100">
                <x-logo size="sm" />
                <button @click="mobileOpen = false" class="btn-ghost p-1.5 -mr-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Nav --}}
            <div class="flex-1 overflow-y-auto py-4 px-3">
                <p class="px-3 text-[10px] font-bold text-ink-400 uppercase tracking-widest mb-2">
                    Shop
                </p>
                <a href="{{ route('shop.products.index') }}"
                   @click="mobileOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                          text-ink-700 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                    All Products
                </a>
                @foreach(app(\App\Services\CacheService::class)->categories() as $cat)
                    <a href="{{ route('shop.products.index', ['category' => $cat->slug]) }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                              text-ink-700 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach

                @auth
                    <div class="mt-4 pt-4 border-t border-ink-100">
                        <p class="px-3 text-[10px] font-bold text-ink-400 uppercase
                                   tracking-widest mb-2">
                            Account
                        </p>
                        @foreach([
                            ['href' => route('profile.edit'),   'label' => 'My Profile'],
                            ['href' => route('orders.index'),   'label' => 'My Orders'],
                            ['href' => route('wishlist.index'), 'label' => 'Wishlist'],
                            ['href' => route('addresses.index'),'label' => 'Addresses'],
                        ] as $link)
                            <a href="{{ $link['href'] }}" @click="mobileOpen = false"
                               class="block px-3 py-2.5 rounded-xl text-sm font-medium
                                      text-ink-700 hover:bg-ink-50 transition-colors">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endauth
            </div>

            {{-- Footer --}}
            <div class="border-t border-ink-100 p-4 space-y-2">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full btn-ghost text-sm text-red-600 hover:bg-red-50
                                       justify-center py-2.5 rounded-xl">
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" @click="mobileOpen = false"
                       class="btn-primary w-full justify-center">Create Account</a>
                    <a href="{{ route('login') }}" @click="mobileOpen = false"
                       class="btn-secondary w-full justify-center">Sign In</a>
                @endauth
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
function navbar() {
    return {
        mobileOpen: false,
        searchOpen: false,

        closeAll() {
            this.mobileOpen = false;
            this.searchOpen = false;
        }
    }
}

function searchBar() {
    return {
        query: '',
        results: [],
        open: false,
        isTrending: false,
        loading: false,
        _trending: [],

        async init() {

            // Preload trending products

            try {

                const res = await fetch(
                    '/shop/products/suggestions?q=',
                    {
                        headers: {
                            Accept: 'application/json'
                        }
                    }
                );

                this._trending = await res.json();

            } catch {

                this._trending = [];
            }
        },

        async onFocus() {

            if (this.query.length < 2) {

                this.results = this._trending || [];

                this.isTrending = true;

                this.open = this.results.length > 0;
            }
        },

        async search() {

            if (this.query.length < 2) {

                this.results = this._trending || [];

                this.isTrending = true;

                this.open = this.results.length > 0;

                return;
            }

            this.isTrending = false;

            this.loading = true;

            try {

                const res = await fetch(
                    `/shop/products/suggestions?q=${encodeURIComponent(this.query)}`,
                    {
                        headers: {
                            Accept: 'application/json'
                        }
                    }
                );

                this.results = await res.json();

                this.open = true;

            } catch {

                this.results = [];

            } finally {

                this.loading = false;
            }
        },

        submitSearch() {

            if (this.query.trim()) {

                window.location.href =
                    `/shop/products?q=${encodeURIComponent(this.query.trim())}`;
            }
        }
    }
}
</script>
@endpush