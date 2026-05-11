<footer class="bg-ink-900 text-white mt-16">

    {{-- Newsletter strip --}}
    <div class="bg-brand-600">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="font-display text-xl text-white">Stay in the loop</p>
                    <p class="text-brand-100 text-sm mt-0.5">New arrivals, exclusive deals, style inspiration.</p>
                </div>
                <form class="flex gap-2 w-full sm:w-auto" @submit.prevent="">
                    <input type="email" placeholder="your@email.com"
                           class="input bg-white/20 border-white/30 text-white placeholder:text-white/60
                                  focus:ring-white/50 rounded-xl flex-1 sm:w-64">
                    <button type="submit"
                            class="flex-shrink-0 bg-white text-brand-700 font-semibold text-sm
                                   px-5 py-2.5 rounded-xl hover:bg-brand-50 transition-colors">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Main footer --}}
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">

            {{-- Brand --}}
            <div class="col-span-2 md:col-span-1">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                        </svg>
                    </div>
                    <span class="font-display text-xl text-white">{{ config('app.name') }}</span>
                </div>
                <p class="text-ink-400 text-sm leading-relaxed">
                    Your trusted destination for quality products. Fast delivery, easy returns.
                </p>
                <div class="flex gap-3 mt-4">
                    @foreach ([
                        ['path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z', 'label' => 'Facebook'],
                        ['path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z', 'label' => 'Instagram'],
                    ] as $social)
                    <a href="#" aria-label="{{ $social['label'] }}"
                       class="w-9 h-9 rounded-lg bg-white/10 hover:bg-brand-600 flex items-center
                              justify-center transition-colors duration-200">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="{{ $social['path'] }}"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Links --}}
            @php $cols = [
                ['title' => 'Shop', 'links' => [
                    ['label' => 'All Products', 'href' => url('/shop/products')],
                    ['label' => 'New Arrivals',  'href' => url('/shop/products?sort=newest')],
                    ['label' => 'Best Sellers',  'href' => url('/shop/products?sort=popular')],
                    ['label' => 'Sale',           'href' => url('/shop/products?sort=price_asc')],
                ]],
                ['title' => 'Customer', 'links' => [
                    ['label' => 'My Orders',    'href' => route('orders.index')],
                    ['label' => 'Track Order',  'href' => route('orders.index')],
                    ['label' => 'Returns',       'href' => '#'],
                    ['label' => 'FAQ',           'href' => '#'],
                ]],
                ['title' => 'Company', 'links' => [
                    ['label' => 'About Us',      'href' => '#'],
                    ['label' => 'Contact',        'href' => '#'],
                    ['label' => 'Privacy Policy', 'href' => '#'],
                    ['label' => 'Terms',          'href' => '#'],
                ]],
            ]; @endphp

            @foreach ($cols as $col)
                <div>
                    <p class="text-white font-semibold text-sm mb-4">{{ $col['title'] }}</p>
                    <ul class="space-y-2.5">
                        @foreach ($col['links'] as $link)
                            <li>
                                <a href="{{ $link['href'] }}"
                                   class="text-ink-400 text-sm hover:text-white transition-colors duration-150">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        {{-- Trust badges --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-10 pt-8 border-t border-white/10">
            @php $trust = [
                ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10', 'label' => 'Free Shipping', 'sub' => 'Orders above ₹999'],
                ['icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'label' => 'Easy Returns', 'sub' => '30-day policy'],
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Secure Payment', 'sub' => '256-bit SSL'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'label' => '24/7 Support', 'sub' => 'Always here for you'],
            ]; @endphp
            @foreach ($trust as $item)
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-semibold">{{ $item['label'] }}</p>
                        <p class="text-ink-400 text-xs mt-0.5">{{ $item['sub'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <p class="text-center text-ink-500 text-xs mt-8 pt-6 border-t border-white/10">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</footer>