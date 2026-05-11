@props([
    'icon'    => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    'title'   => 'Nothing here yet',
    'message' => null,
    'action'  => null,
    'actionLabel' => 'Get Started',
    'size'    => 'md',
])

@php
    $pad   = $size === 'sm' ? 'py-10' : 'py-20';
    $iSize = $size === 'sm' ? 'w-14 h-14' : 'w-20 h-20';
    $svgS  = $size === 'sm' ? 'w-6 h-6'  : 'w-9 h-9';
@endphp

<div class="text-center {{ $pad }} animate-fade-in">
    <div class="{{ $iSize }} bg-ink-100 rounded-2xl flex items-center
                justify-center mx-auto mb-4">
        <svg class="{{ $svgS }} text-ink-300" fill="none" stroke="currentColor"
             stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
        </svg>
    </div>
    <p class="font-semibold text-ink-700 {{ $size === 'sm' ? 'text-base' : 'text-lg' }} mb-1">
        {{ $title }}
    </p>
    @if($message)
        <p class="text-ink-400 text-sm mt-1 max-w-xs mx-auto leading-relaxed">
            {{ $message }}
        </p>
    @endif
    {{ $slot }}
    @if($action)
        <a href="{{ $action }}" class="btn-primary mt-5 inline-flex">
            {{ $actionLabel }}
        </a>
    @endif
</div>