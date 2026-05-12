<footer class="bg-ink-900 text-white mt-20">

    {{-- Newsletter --}}
    <div class="bg-gradient-to-r from-brand-700 to-brand-600">
        <div class="max-w-7xl mx-auto px-4 py-8 sm:py-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center
                        justify-between gap-5">
                <div class="flex-1">
                    <p class="font-display text-2xl text-white">
                        Stay inspired.
                    </p>
                    <p class="text-brand-100 text-sm mt-1 max-w-sm">
                        New arrivals, exclusive deals, and curated picks —
                        straight to your inbox.
                    </p>
                </div>
                <form class="flex gap-2 w-full sm:w-auto sm:max-w-sm" @submit.prevent="">
                    <input type="email" placeholder="your@email.com"
                           class="flex-1 sm:w-60 px-4 py-2.5 bg-white/15 border border-white/25
                                  rounded-xl text-sm text-white placeholder:text-white/50
                                  focus:outline-none focus:ring-2 focus:ring-white/40
                                  focus:bg-white/20 transition-all">
                    <button type="submit"
                            class="flex-shrink-0 bg-white text-brand-700 font-semibold text-sm
                                   px-5 py-2.5 rounded-xl hover:bg-brand-50 transition-colors
                                   shadow-lg">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main footer --}}
    <div class="max-w-7xl mx-auto px-4 pt-12 pb-8">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8 mb-10">

            {{-- Brand col --}}
            <div class="col-span-2 md:col-span-2 pr-0 md:pr-8">
                <x-logo size="md" variant="white" class="mb-4" />
                <p class="text-ink-400 text-sm leading-relaxed mt-4 max-w-xs">
                    {{ config('brand.description') }}
                </p>
                {{-- Social --}}
                <div class="flex gap-2.5 mt-5">
                    @foreach([
                        ['href' => config('brand.social.instagram'), 'label' => 'Instagram',
                         'd' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z'],
                        ['href' => config('brand.social.twitter'), 'label' => 'Twitter',
                         'd' => 'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z'],
                    ] as $s)
                        <a href="{{ $s['href'] }}" target="_blank" rel="noopener"
                           aria-label="{{ $s['label'] }}"
                           class="w-9 h-9 bg-white/10 hover:bg-brand-600 rounded-lg
                                  flex items-center justify-center transition-all
                                  duration-200 hover:scale-105">
                            <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24">
                                <path d="{{ $s['d'] }}"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Link columns --}}
            @php $cols = [
                ['title' => 'Shop', 'links' => [
                    ['label' => 'All Products',  'href' => route('shop.products.index')],
                    ['label' => 'New Arrivals',   'href' => route('shop.products.index', ['sort' => 'newest'])],
                    ['label' => 'Best Sellers',   'href' => route('shop.products.index', ['sort' => 'popular'])],
                    ['label' => 'Top Rated',      'href' => route('shop.products.index', ['sort' => 'rating'])],
                    ['label' => 'On Sale',         'href' => route('shop.products.index', ['sort' => 'price_asc'])],
                ]],
                ['title' => 'Support', 'links' => [
                    ['label' => 'My Orders',      'href' => route('orders.index')],
                    ['label' => 'Track Order',     'href' => route('orders.index')],
                    ['label' => 'Returns Policy',  'href' => '#'],
                    ['label' => 'FAQs',            'href' => '#'],
                    ['label' => 'Contact Us',      'href' => 'mailto:'.config('brand.support')],
                ]],
                ['title' => 'Company', 'links' => [
                    ['label' => 'About Us',        'href' => '#'],
                    ['label' => 'Careers',          'href' => '#'],
                    ['label' => 'Press',            'href' => '#'],
                    ['label' => 'Privacy Policy',   'href' => '#'],
                    ['label' => 'Terms of Service', 'href' => '#'],
                ]],
            ]; @endphp

            @foreach($cols as $col)
                <div>
                    <p class="text-white font-semibold text-sm mb-4">{{ $col['title'] }}</p>
                    <ul class="space-y-2.5">
                        @foreach($col['links'] as $link)
                            <li>
                                <a href="{{ $link['href'] }}"
                                   class="text-ink-400 text-sm hover:text-white
                                          transition-colors duration-150 hover:translate-x-0.5
                                          inline-block">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- Trust badges --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-8 border-t border-white/10 mb-8">
            @foreach([
                ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10',
                 'title' => 'Free Shipping',  'sub' => 'Orders above ₹'.config('brand.free_shipping_threshold')],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                 'title' => 'Easy Returns',   'sub' => '30-day policy'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                 'title' => 'Secure Pay',     'sub' => '256-bit SSL'],
                ['icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
                 'title' => '24/7 Support',   'sub' => 'Always here for you'],
            ] as $t)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center
                                justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor"
                             stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-semibold">{{ $t['title'] }}</p>
                        <p class="text-ink-500 text-xs mt-0.5">{{ $t['sub'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3
                    pt-6 border-t border-white/10">
            <p class="text-ink-500 text-xs">
                © {{ date('Y') }}
                <span class="font-semibold text-ink-400">
                    {{ config('brand.name') }}
                </span>
                · All rights reserved.
            </p>
            <div class="flex items-center gap-2">
                {{-- Payment icons --}}
                @foreach(['VISA', 'MC', 'UPI', 'GPay'] as $pm)
                    <span class="inline-flex items-center px-2 py-1 bg-white/10 rounded
                                 text-[9px] font-bold text-ink-400 tracking-wider">
                        {{ $pm }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</footer>