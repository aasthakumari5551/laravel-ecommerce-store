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
</body>
</html>