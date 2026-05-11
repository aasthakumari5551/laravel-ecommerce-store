{{-- Sticky navbar with glass effect, search, cart/wishlist counters --}}
<header class="sticky top-0 z-50 nav-glass" x-data="navbar()" @keydown.escape.window="closeAll()">

    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center gap-3 h-16">

            {{-- ── Mobile menu button ───────────────────────── --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden btn-ghost p-2 -ml-2 flex-shrink-0"
                    :aria-expanded="mobileOpen">
                <svg class="w-5 h-5 transition-transform duration-200" :class="mobileOpen ? 'rotate-90' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileOpen"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- ── Logo ──────────────────────────────────────── --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 flex-shrink-0 group">
                <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center
                            group-hover:bg-brand-700 transition-colors duration-150 shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                    </svg>
                </div>
                <span class="font-display text-xl text-ink-900 leading-none hidden xs:block">
                    {{ config('app.name') }}
                </span>
            </a>

            {{-- ── Search bar (desktop) ──────────────────────── --}}
            <div class="flex-1 max-w-xl hidden md:block relative" x-data="searchBar()">
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                    </div>
                    <input type="text"
                           x-model="query"
                           @input.debounce.300ms="search()"
                           @focus="open = true"
                           @click.outside="open = false"
                           placeholder="Search products, brands…"
                           class="input pl-9 pr-4 py-2 text-sm bg-ink-50 border-ink-200 focus:bg-white rounded-xl">
                    <div x-show="open && results.length > 0"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         class="absolute top-full left-0 right-0 mt-1.5 bg-white border border-ink-100
                                rounded-xl shadow-lg overflow-hidden z-50">
                        <template x-for="item in results" :key="item.uuid">
                            <a :href="'/shop/products/' + item.uuid"
                               class="flex items-center justify-between px-4 py-2.5 hover:bg-ink-50
                                      text-sm text-ink-900 transition-colors border-b border-ink-50 last:border-0">
                                <span x-text="item.name"></span>
                                <span class="text-ink-500 text-xs font-mono" x-text="'₹' + parseFloat(item.price).toFixed(2)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ── Right actions ─────────────────────────────── --}}
            <div class="flex items-center gap-1 ml-auto lg:ml-0">

                {{-- Search icon (mobile) --}}
                <button @click="searchOpen = !searchOpen"
                        class="md:hidden btn-ghost p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </button>

                {{-- Wishlist --}}
                @auth
                    <a href="{{ route('wishlist.index') }}"
                       class="btn-ghost p-2 relative hidden sm:flex">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        @php $wishlistCount = auth()->user()->wishlist?->totalItems() ?? 0; @endphp
                        @if ($wishlistCount > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-brand-500 text-white text-[10px]
                                         font-bold rounded-full flex items-center justify-center">
                                {{ min($wishlistCount, 9) }}{{ $wishlistCount > 9 ? '+' : '' }}
                            </span>
                        @endif
                    </a>
                @endauth

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                   class="btn-ghost p-2 relative flex items-center gap-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    @php
                        $cartCount = auth()->check()
                            ? (auth()->user()->cart?->totalItems() ?? 0)
                            : 0;
                    @endphp
                    @if ($cartCount > 0)
                        <span class="absolute top-1 right-1 w-4 h-4 bg-brand-600 text-white text-[10px]
                                     font-bold rounded-full flex items-center justify-center">
                            {{ min($cartCount, 9) }}{{ $cartCount > 9 ? '+' : '' }}
                        </span>
                    @endif
                    <span class="hidden sm:block text-xs font-medium text-ink-700">
                        @if ($cartCount > 0) {{ $cartCount }} item{{ $cartCount > 1 ? 's' : '' }} @else Cart @endif
                    </span>
                </a>

                {{-- Auth dropdown --}}
                @auth
                    <div class="relative hidden sm:block" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="flex items-center gap-2 btn-ghost px-3 py-2">
                            <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center
                                        text-brand-700 text-xs font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-ink-800 max-w-[80px] truncate hidden lg:block">
                                {{ auth()->user()->name }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-ink-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             class="absolute right-0 top-full mt-1.5 w-52 bg-white rounded-xl
                                    border border-ink-100 shadow-lg overflow-hidden z-50 py-1">
                            <div class="px-4 py-2.5 border-b border-ink-50">
                                <p class="text-xs font-semibold text-ink-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-ink-400 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            @php $menuLinks = [
                                ['href' => route('profile.edit'),  'label' => 'My Profile',  'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                ['href' => route('orders.index'),  'label' => 'My Orders',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                ['href' => route('wishlist.index'),'label' => 'Wishlist',    'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                                ['href' => route('addresses.index'),'label' => 'Addresses',  'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                            ]; @endphp
                            @foreach ($menuLinks as $link)
                                <a href="{{ $link['href'] }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-ink-700
                                          hover:bg-ink-50 hover:text-ink-900 transition-colors">
                                    <svg class="w-4 h-4 text-ink-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
                                    </svg>
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.analytics.dashboard') }}"
                                   class="flex items-center gap-3 px-4 py-2 text-sm text-brand-700
                                          hover:bg-brand-50 transition-colors border-t border-ink-50 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Admin Panel
                                </a>
                            @endif
                            <div class="border-t border-ink-50 mt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2 text-sm
                                                   text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('login') }}" class="btn-ghost px-3 py-2 text-sm">Sign In</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm">Register</a>
                    </div>
                @endauth
            </div>
        </div>

        {{-- ── Mobile search bar ─────────────────────────── --}}
        <div x-show="searchOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             class="md:hidden pb-3" x-data="searchBar()">
            <div class="relative">
                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                </div>
                <input type="text" x-model="query" @input.debounce.300ms="search()"
                       placeholder="Search products…"
                       class="input pl-9 text-sm rounded-xl">
            </div>
        </div>

        {{-- ── Category nav strip ────────────────────────── --}}
        <nav class="hidden lg:block border-t border-ink-100/60" aria-label="Categories">
            <div class="flex items-center gap-1 overflow-x-auto scrollbar-none py-1">
                <a href="{{ url('/shop/products') }}"
                   class="flex-shrink-0 px-4 py-2 text-sm font-medium text-ink-600
                          hover:text-ink-900 hover:bg-ink-50 rounded-lg transition-colors
                          {{ request()->routeIs('shop.products.index') && !request()->has('category')
                              ? 'text-brand-700 font-semibold' : '' }}">
                    All Products
                </a>
                @foreach (app(\App\Services\CacheService::class)->categories() as $cat)
                    <a href="{{ route('shop.products.index', ['category' => $cat->slug]) }}"
                       class="flex-shrink-0 px-4 py-2 text-sm font-medium text-ink-600
                              hover:text-ink-900 hover:bg-ink-50 rounded-lg transition-colors
                              {{ request('category') === $cat->slug ? 'text-brand-700 font-semibold' : '' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </nav>
    </div>

    {{-- ── Mobile drawer menu ────────────────────────────── --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="lg:hidden fixed inset-0 z-40" @click.self="mobileOpen = false">

        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="-translate-x-full"
             class="absolute inset-y-0 left-0 w-72 bg-white shadow-2xl flex flex-col overflow-y-auto">

            <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
                <span class="font-display text-lg text-ink-900">Menu</span>
                <button @click="mobileOpen = false" class="btn-ghost p-1.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 py-4 px-3 space-y-1">
                <p class="px-3 text-xs font-semibold text-ink-400 uppercase tracking-widest mb-2">Categories</p>
                <a href="{{ url('/shop/products') }}" @click="mobileOpen = false"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                          text-ink-700 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                    All Products
                </a>
                @foreach (app(\App\Services\CacheService::class)->categories() as $cat)
                    <a href="{{ route('shop.products.index', ['category' => $cat->slug]) }}"
                       @click="mobileOpen = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                              text-ink-700 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <div class="border-t border-ink-100 p-4 space-y-2">
                @auth
                    <a href="{{ route('orders.index') }}" class="btn-secondary w-full justify-center text-sm">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-ghost w-full justify-center text-sm text-red-600 hover:bg-red-50">
                            Sign Out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary w-full justify-center">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-primary w-full justify-center">Register</a>
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
        closeAll() { this.mobileOpen = false; this.searchOpen = false; }
    }
}

function searchBar() {
    return {
        query: '',
        results: [],
        open: false,
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            try {
                const res = await fetch(`/shop/products/suggestions?q=${encodeURIComponent(this.query)}`);
                this.results = await res.json();
                this.open = true;
            } catch { this.results = []; }
        }
    }
}
</script>
@endpush