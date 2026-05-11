@props(['title', 'subtitle' => null, 'link' => null, 'linkLabel' => 'View All'])

<div class="flex items-end justify-between mb-6 sm:mb-8">
    <div>
        <h2 class="section-title">{{ $title }}</h2>
        @if ($subtitle)
            <p class="text-sm text-ink-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if ($link)
        <a href="{{ $link }}"
           class="flex-shrink-0 flex items-center gap-1.5 text-sm font-medium
                  text-brand-700 hover:text-brand-800 transition-colors group">
            {{ $linkLabel }}
            <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif
</div>