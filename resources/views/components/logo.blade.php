@props([
    'size'    => 'md',
    'variant' => 'default',  // default | white | dark
    'showTag' => true,
])

@php
    $sizes = [
        'xs' => ['mark' => 'w-6 h-6',  'text' => 'text-base',  'gap' => 'gap-2'],
        'sm' => ['mark' => 'w-7 h-7',  'text' => 'text-lg',    'gap' => 'gap-2'],
        'md' => ['mark' => 'w-8 h-8',  'text' => 'text-xl',    'gap' => 'gap-2.5'],
        'lg' => ['mark' => 'w-11 h-11','text' => 'text-2xl',   'gap' => 'gap-3'],
        'xl' => ['mark' => 'w-14 h-14','text' => 'text-3xl',   'gap' => 'gap-3.5'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];

    $textColor = match($variant) {
        'white' => 'text-white',
        'dark'  => 'text-ink-900',
        default => 'text-ink-900',
    };
    $subColor = match($variant) {
        'white' => 'text-white/50',
        default => 'text-ink-400',
    };
@endphp

<div class="flex items-center {{ $s['gap'] }}">
    {{-- Mark: V-shaped geometric gem --}}
    <div class="{{ $s['mark'] }} relative flex-shrink-0">
        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"
             class="w-full h-full">
            {{-- Background square --}}
            <rect width="32" height="32" rx="8" fill="#D97706"/>
            {{-- Inner gem facets --}}
            <path d="M16 6L26 12V20L16 26L6 20V12L16 6Z"
                  fill="white" fill-opacity="0.15"/>
            <path d="M16 6L26 12L16 18L6 12L16 6Z"
                  fill="white" fill-opacity="0.25"/>
            <path d="M16 18L26 12V20L16 26V18Z"
                  fill="black" fill-opacity="0.12"/>
            <path d="M16 18L6 12V20L16 26V18Z"
                  fill="black" fill-opacity="0.06"/>
            {{-- V letterform --}}
            <path d="M11 11L16 22L21 11"
                  stroke="white" stroke-width="2.5" stroke-linecap="round"
                  stroke-linejoin="round" fill="none"/>
        </svg>
    </div>

    {{-- Wordmark --}}
    @if($showTag)
        <div>
            <span class="font-display {{ $s['text'] }} {{ $textColor }} leading-none tracking-tight">
                {{ config('brand.name') }}
            </span>
            @if($size === 'lg' || $size === 'xl')
                <p class="text-xs {{ $subColor }} mt-0.5 tracking-widest uppercase">
                    {{ config('brand.tagline') }}
                </p>
            @endif
        </div>
    @endif
</div>