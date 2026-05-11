@props(['label', 'value', 'delta' => null, 'icon', 'color' => 'brand', 'href' => null])

@php
    $colors = [
        'brand'  => ['bg' => 'bg-brand-50',  'icon' => 'text-brand-600',  'ring' => 'ring-brand-100'],
        'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-600',  'ring' => 'ring-green-100'],
        'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-600',   'ring' => 'ring-blue-100'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-600', 'ring' => 'ring-purple-100'],
        'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-600',    'ring' => 'ring-red-100'],
    ];
    $c = $colors[$color] ?? $colors['brand'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} {{ $href ? "href={$href}" : '' }}
    class="bg-white rounded-xl border border-ink-100 shadow-card p-5
           {{ $href ? 'hover:border-brand-200 hover:shadow-card-hover transition-all group block' : '' }}">
    <div class="flex items-start justify-between mb-4">
        <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} ring-4 {{ $c['ring'] }}
                    flex items-center justify-center">
            <svg class="w-4.5 h-4.5 {{ $c['icon'] }}" fill="none" stroke="currentColor"
                 stroke-width="1.75" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
        </div>
        @if($delta !== null)
            @php $pos = $delta >= 0; @endphp
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                         {{ $pos ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                {{ $pos ? '↑' : '↓' }} {{ abs($delta) }}%
            </span>
        @endif
    </div>
    <p class="text-2xl font-bold text-ink-900 leading-none mb-1.5">{{ $value }}</p>
    <p class="text-xs text-ink-400 font-medium uppercase tracking-wider">{{ $label }}</p>
</{{ $tag }}>