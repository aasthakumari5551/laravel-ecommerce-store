@props([
    'products',
    'title'      => null,
    'subtitle'   => null,
    'link'       => null,
    'linkLabel'  => 'View All',
    'id'         => 'carousel-' . uniqid(),
    'badge'      => null,
    'badgeColor' => 'brand',
])

@php
    $badgeClasses = match($badgeColor) {
        'purple' => 'bg-violet-600',
        'blue'   => 'bg-sky-600',
        default  => 'bg-brand-600',
    };
@endphp

@if($products->isNotEmpty())

<section
    class="relative py-10 md:py-14"
    x-data="{
        scroll(dir) {
            const track = this.$refs.track;
            if (!track) return;

            const item = track.querySelector('[data-carousel-item]');
            const gap = parseInt(getComputedStyle(track).gap, 10) || 20;

            const step = item
                ? item.offsetWidth + gap
                : 320;

            track.scrollBy({
                left: dir * step,
                behavior: 'smooth'
            });
        }
    }"
>

    {{-- Header --}}
    @if($title)

        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between mb-8 md:mb-10">

            <div class="min-w-0">

                <div class="flex items-center flex-wrap gap-3 mb-2">

                    @if($badge)
                        <span class="{{ $badgeClasses }}
                                     text-white text-[10px]
                                     px-3 py-1 rounded-full
                                     tracking-[0.2em] uppercase font-semibold">

                            {{ $badge }}

                        </span>
                    @endif

                    <h2 class="text-2xl md:text-3xl font-semibold tracking-tight text-ink-900">
                        {{ $title }}
                    </h2>

                </div>

                @if($subtitle)

                    <p class="text-sm md:text-base text-ink-500 max-w-2xl leading-relaxed">
                        {{ $subtitle }}
                    </p>

                @endif

            </div>

            <div class="flex items-center justify-between md:justify-end gap-4 shrink-0">

                {{-- Controls --}}
                <div class="flex items-center gap-2">

                    <button
                        type="button"
                        @click="scroll(-1)"
                        aria-label="Scroll left"
                        class="w-11 h-11 rounded-full bg-white border border-ink-200/80
                               flex items-center justify-center text-ink-600
                               hover:border-brand-400 hover:text-brand-600
                               hover:shadow-md active:scale-95
                               transition-all duration-200"
                    >

                        <svg class="w-4 h-4"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />

                        </svg>

                    </button>

                    <button
                        type="button"
                        @click="scroll(1)"
                        aria-label="Scroll right"
                        class="w-11 h-11 rounded-full bg-white border border-ink-200/80
                               flex items-center justify-center text-ink-600
                               hover:border-brand-400 hover:text-brand-600
                               hover:shadow-md active:scale-95
                               transition-all duration-200"
                    >

                        <svg class="w-4 h-4"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />

                        </svg>

                    </button>

                </div>

                {{-- Link --}}
                @if($link)

                    <a href="{{ $link }}"
                       class="inline-flex items-center gap-1.5
                              text-sm font-semibold text-brand-700
                              hover:text-brand-800 transition-colors group">

                        {{ $linkLabel }}

                        <svg class="w-4 h-4 transition-transform duration-200
                                    group-hover:translate-x-0.5"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5l7 7-7 7"
                            />

                        </svg>

                    </a>

                @endif

            </div>

        </div>

    @endif

    {{-- Carousel --}}
    <div class="relative">

        {{-- Left Fade --}}
        <div class="hidden md:block pointer-events-none absolute inset-y-0 left-0 z-10 w-16
                    bg-gradient-to-r from-ink-50 to-transparent">
        </div>

        {{-- Right Fade --}}
        <div class="hidden md:block pointer-events-none absolute inset-y-0 right-0 z-10 w-16
                    bg-gradient-to-l from-ink-50 to-transparent">
        </div>

        <div
            id="{{ $id }}"
            x-ref="track"
            role="region"
            aria-roledescription="carousel"
            aria-label="{{ $title ?? 'Products' }}"
            tabindex="0"
            class="flex gap-5 overflow-x-auto overscroll-x-contain
                   scroll-smooth snap-x snap-mandatory
                   scrollbar-none touch-pan-x
                   pb-3 scroll-px-4"
        >

            @foreach($products as $product)

                <div
                    data-carousel-item
                    class="w-[72vw] max-w-[280px]
                           sm:w-[240px]
                           md:w-[260px]
                           shrink-0 snap-start"
                >

                    <x-product-card :product="$product" />

                </div>

            @endforeach

        </div>

    </div>

</section>

@endif