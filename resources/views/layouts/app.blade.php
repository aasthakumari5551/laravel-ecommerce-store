<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <meta name="description" content="@yield('meta_description', 'Shop the latest trends.')">

    {{-- Fonts are loaded via CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>


<body class="min-h-screen flex flex-col">

    {{-- ── Top announcement bar ─────────────────────────── --}}
    <div class="bg-ink-900 text-ink-100 text-xs text-center py-2 px-4 tracking-wide hidden sm:block">
        🚚 Free delivery on orders above ₹999 &nbsp;·&nbsp;
        <span class="text-brand-300">Use code <strong>WELCOME10</strong> for 10% off your first order</span>
    </div>

    {{-- ── Navbar ──────────────────────────────────────── --}}
    @include('layouts.partials.navbar')

    {{-- ── Flash messages ──────────────────────────────── --}}
    @if (session()->hasAny(['success', 'error', 'warning']))
        <div class="max-w-7xl mx-auto w-full px-4 pt-4" x-data="{ show: true }" x-show="show"
             x-transition:leave="transition-opacity duration-300" x-transition:leave-end="opacity-0">
            @if (session('success'))
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800
                            rounded-xl px-4 py-3 text-sm shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                        </svg>
                    </button>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800
                            rounded-xl px-4 py-3 text-sm shadow-sm">
                    <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- ── Page content ─────────────────────────────────── --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ── Footer ──────────────────────────────────────── --}}
    @include('layouts.partials.footer')

    @stack('scripts')

    {{-- Alpine.js for interactive components --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Simple AOS (animate on scroll) without a library --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const obs = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('aos-animate');
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.08 });
            document.querySelectorAll('[data-aos]').forEach(el => obs.observe(el));
        });
    </script>

    {{-- Place just before </body> in layouts/app.blade.php --}}
<div x-data="cartDrawer()"
     @cart-open.window="open = true"
     @keydown.escape.window="open = false">

    {{-- Backdrop --}}
    <div x-show="open"
         @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-ink-900/50 backdrop-blur-sm z-[60]"
         x-cloak>
    </div>

    {{-- Drawer --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-full max-w-sm bg-white z-[70]
                flex flex-col shadow-2xl"
         x-cloak>

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-ink-100">
            <h2 class="font-semibold text-ink-900 flex items-center gap-2">
                Cart
                <span class="badge bg-brand-100 text-brand-700 text-xs px-2 py-0.5"
                      x-text="itemCount + ' item' + (itemCount !== 1 ? 's' : '')">
                </span>
            </h2>
            <button @click="open = false" class="btn-ghost p-1.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Free shipping progress --}}
        <div class="px-5 py-3 bg-ink-50 border-b border-ink-100">
            <template x-if="subtotal < {{ config('brand.free_shipping_threshold') }}">
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-ink-600 font-medium">
                            Add ₹<span x-text="({{ config('brand.free_shipping_threshold') }} - subtotal).toLocaleString('en-IN')"></span> for free shipping
                        </span>
                        <span class="text-brand-600 font-bold">
                            ₹{{ config('brand.free_shipping_threshold') }} threshold
                        </span>
                    </div>
                    <div class="w-full bg-ink-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-brand-500 h-1.5 rounded-full transition-all duration-500"
                             :style="'width:' + Math.min(100, (subtotal / {{ config('brand.free_shipping_threshold') }}) * 100) + '%'">
                        </div>
                    </div>
                </div>
            </template>
            <template x-if="subtotal >= {{ config('brand.free_shipping_threshold') }}">
                <div class="flex items-center gap-2 text-green-700">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/>
                    </svg>
                    <span class="text-xs font-semibold">🎉 You've unlocked free shipping!</span>
                </div>
            </template>
        </div>

        {{-- Items --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
            <template x-if="loading">
                <div class="space-y-4">
                    <template x-for="i in 3">
                        <div class="flex gap-3">
                            <div class="w-16 h-16 skeleton rounded-xl flex-shrink-0"></div>
                            <div class="flex-1 space-y-2">
                                <div class="skeleton h-3 rounded w-3/4"></div>
                                <div class="skeleton h-3 rounded w-1/2"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-ink-100 rounded-2xl flex items-center
                                justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-ink-300" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="1.5"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <p class="text-ink-500 text-sm font-medium">Your cart is empty</p>
                    <button @click="open = false"
                            class="btn-primary mt-4 text-sm">Start Shopping</button>
                </div>
            </template>

            <template x-for="item in items" :key="item.id">
                <div class="flex gap-3 group animate-fade-in">
                    <a :href="'/shop/products/' + item.product.uuid"
                       class="w-16 h-16 rounded-xl overflow-hidden bg-ink-50 flex-shrink-0
                              border border-ink-100">
                        <img :src="item.product.primary_image_url || '/placeholder.svg'"
                             :alt="item.product_name"
                             class="w-full h-full object-cover">
                    </a>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink-900 line-clamp-1"
                           x-text="item.product_name"></p>
                        <p class="text-xs text-ink-400 mt-0.5"
                           x-text="'₹' + parseFloat(item.unit_price).toLocaleString('en-IN') + ' each'">
                        </p>
                        <div class="flex items-center justify-between mt-2">
                            {{-- Qty stepper --}}
                            <div class="flex items-center border border-ink-200 rounded-lg overflow-hidden">
                                <button @click="updateQty(item, item.quantity - 1)"
                                        class="w-7 h-7 flex items-center justify-center
                                               text-ink-500 hover:bg-ink-50 transition-colors
                                               text-sm font-medium">
                                    −
                                </button>
                                <span class="w-7 text-center text-xs font-semibold text-ink-900"
                                      x-text="item.quantity"></span>
                                <button @click="updateQty(item, item.quantity + 1)"
                                        class="w-7 h-7 flex items-center justify-center
                                               text-ink-500 hover:bg-ink-50 transition-colors
                                               text-sm font-medium">
                                    +
                                </button>
                            </div>
                            <p class="text-sm font-bold text-ink-900"
                               x-text="'₹' + (item.unit_price * item.quantity).toLocaleString('en-IN')">
                            </p>
                        </div>
                    </div>
                    {{-- Remove --}}
                    <button @click="removeItem(item)"
                            class="self-start w-6 h-6 rounded-md flex items-center justify-center
                                   text-ink-300 hover:text-red-500 hover:bg-red-50
                                   transition-all opacity-0 group-hover:opacity-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-ink-100 px-5 py-4 space-y-3 bg-white">
            {{-- Subtotal --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-ink-600">Subtotal</span>
                <span class="font-bold text-ink-900 text-lg"
                      x-text="'₹' + subtotal.toLocaleString('en-IN')"></span>
            </div>
            <p class="text-xs text-ink-400">Taxes and shipping calculated at checkout.</p>

            <a href="{{ route('checkout.index') }}"
               class="btn-primary w-full justify-center py-3 rounded-xl text-base">
                Checkout
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="{{ route('cart.index') }}"
               class="block text-center text-xs text-ink-400 hover:text-ink-700
                      transition-colors">
                View full cart →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cartDrawer() {
    return {
        open: false,
        loading: false,
        items: [],
        subtotal: 0,
        itemCount: 0,

        async init() {
            this.$watch('open', val => { if (val) this.fetchCart(); });
        },

        async fetchCart() {
            this.loading = true;
            try {
                const res  = await fetch('/api/cart', { headers: { Accept: 'application/json' } });
                const data = await res.json();
                this.items     = data.items     || [];
                this.subtotal  = data.subtotal  || 0;
                this.itemCount = data.total_items || 0;
            } catch { window.toast.error('Could not load cart'); }
            finally { this.loading = false; }
        },

        async updateQty(item, qty) {
            if (qty < 1) { this.removeItem(item); return; }
            try {
                const fd = new FormData();
                fd.append('_method',  'PATCH');
                fd.append('_token',   document.querySelector('meta[name=csrf-token]').content);
                fd.append('quantity', qty);
                const res = await fetch(`/cart/${item.id}`, { method: 'POST', body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }});
                if (res.ok) await this.fetchCart();
            } catch { window.toast.error('Could not update item'); }
        },

        async removeItem(item) {
            try {
                const fd = new FormData();
                fd.append('_method', 'DELETE');
                fd.append('_token',  document.querySelector('meta[name=csrf-token]').content);
                const res = await fetch(`/cart/${item.id}`, { method: 'POST', body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }});
                if (res.ok) { await this.fetchCart(); window.toast.info('Item removed'); }
            } catch { window.toast.error('Could not remove item'); }
        },
    }
}

// Open drawer when cart icon clicked
document.querySelectorAll('[data-open-cart]').forEach(el => {
    el.addEventListener('click', e => {
        e.preventDefault();
        window.dispatchEvent(new CustomEvent('cart-open'));
    });
});
</script>
@endpush

{{-- Global components --}}
@include('components.cart-drawer')
@include('components.quick-view-modal')
@include('components.compare-bar')

{{-- Save search on form submit --}}
<script>
document.addEventListener('submit', e => {
    const form = e.target;
    if (!form.action?.includes('/shop/products')) return;
    const q = form.querySelector('[name=q]')?.value?.trim();
    if (q) window.recentSearches.add(q);
});

// Also save from URL on page load
(function() {
    const q = new URLSearchParams(location.search).get('q');
    if (q) window.recentSearches.add(q);
})();
</script>
</body>
</html>