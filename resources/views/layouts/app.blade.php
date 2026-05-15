<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO --}}
    <title>@yield('title', config('brand.name')) — {{ config('brand.name') }}</title>
    <meta name="description"
          content="@yield('meta_description', config('brand.description'))">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type', 'website')">
    <meta property="og:title"
          content="@yield('og_title', config('brand.name') . ' — ' . config('brand.tagline'))">
    <meta property="og:description"
          content="@yield('meta_description', config('brand.description'))">
    <meta property="og:url"         content="{{ url()->current() }}">
    <meta property="og:site_name"   content="{{ config('brand.name') }}">
    <meta property="og:image"
          content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter card --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"
          content="@yield('og_title', config('brand.name'))">
    <meta name="twitter:description"
          content="@yield('meta_description', config('brand.description'))">
    <meta name="twitter:image"
          content="@yield('og_image', asset('images/og-default.jpg'))">

    {{-- Favicon (inline SVG as data URI — no file needed) --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg'
          viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23D97706'/>
          <path d='M11 11L16 22L21 11' stroke='white' stroke-width='2.5'
          stroke-linecap='round' stroke-linejoin='round'/></svg>">

    {{-- Preconnect --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="min-h-screen flex flex-col bg-[#faf9f6]"
      x-data="{ cartCount: {{ auth()->check() ? (auth()->user()->cart?->totalItems() ?? 0) : 0 }} }">

    {{-- Skip to content (accessibility) --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4
              focus:z-[100] focus:px-4 focus:py-2 focus:bg-brand-600 focus:text-white
              focus:rounded-lg focus:text-sm focus:font-medium">
        Skip to main content
    </a>

    {{-- Announcement bar --}}
    <div class="bg-ink-900 text-ink-300 text-xs text-center py-2 px-4
                hidden sm:block" role="banner">
        🚚 Free delivery above
        <strong class="text-brand-400">
            ₹{{ config('brand.free_shipping_threshold') }}
        </strong>
        &nbsp;·&nbsp;
        Use code <strong class="text-white">WELCOME10</strong>
        for 10% off your first order
    </div>

    {{-- Navbar --}}
    @include('layouts.partials.navbar')

    {{-- Flash messages --}}
    @if(session()->hasAny(['success', 'error', 'warning', 'info']))
        <div class="max-w-7xl mx-auto w-full px-4 pt-4 space-y-2"
             role="alert" aria-live="polite">
            @foreach(['success' => 'success', 'error' => 'error',
                       'warning' => 'warning', 'info' => 'info'] as $type => $key)
                @if(session($key))
                    <x-alert :type="$type">{{ session($key) }}</x-alert>
                @endif
            @endforeach
            @if($errors->any() && !$errors->has('checkout') && !$errors->has('payment'))
                <x-alert type="error" :dismissible="false">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif
        </div>
    @endif

    {{-- Main --}}
    <main id="main-content" class="flex-1" role="main">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('layouts.partials.footer')

    {{-- Global modals --}}
    @include('components.cart-drawer')
    @include('components.quick-view-modal')
    @include('components.compare-bar')

    {{-- Alpine (defer) --}}
    <script defer
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js">
    </script>

    {{-- Lightweight AOS --}}
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('aos-animate');
                    obs.unobserve(e.target);
                }
            });
        }, { threshold: 0.06 });
        document.querySelectorAll('[data-aos]').forEach(el => obs.observe(el));
    });
    </script>

    @stack('scripts')

    {{-- Save recent search from URL on load --}}
    <script>
    (function() {
        const q = new URLSearchParams(location.search).get('q');
        if (q && window.recentSearches) window.recentSearches.add(q);
    })();
    </script>
</body>
</html>