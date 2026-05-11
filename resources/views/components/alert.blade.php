@props([
    'type'        => 'info',
    'dismissible' => true,
    'icon'        => null,
])

@php
    $styles = [
        'success' => ['bg' => 'bg-green-50 border-green-200', 'text' => 'text-green-800', 'icon_color' => 'text-green-500',
                      'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        'error'   => ['bg' => 'bg-red-50 border-red-200',   'text' => 'text-red-800',   'icon_color' => 'text-red-500',
                      'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'warning' => ['bg' => 'bg-amber-50 border-amber-200','text' => 'text-amber-800','icon_color' => 'text-amber-500',
                      'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
        'info'    => ['bg' => 'bg-blue-50 border-blue-200',  'text' => 'text-blue-800', 'icon_color' => 'text-blue-500',
                      'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
    ];
    $s = $styles[$type] ?? $styles['info'];
    $iconPath = $icon ?? $s['icon'];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:leave="transition-opacity duration-300"
     x-transition:leave-end="opacity-0"
     class="{{ $s['bg'] }} border {{ $s['text'] }} rounded-xl px-4 py-3 flex items-start gap-3 text-sm"
     role="alert">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 {{ $s['icon_color'] }}"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
    </svg>
    <span class="flex-1 leading-relaxed">{{ $slot }}</span>
    @if($dismissible)
        <button @click="show = false"
                class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
            </svg>
        </button>
    @endif
</div>