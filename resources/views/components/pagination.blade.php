@if ($paginator->hasPages())
    <nav class="flex items-center gap-1" aria-label="Pagination">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-ink-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg border border-ink-200
                      text-ink-600 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="w-9 h-9 flex items-center justify-center text-ink-400 text-sm">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-lg
                                     bg-brand-600 text-white text-sm font-semibold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="w-9 h-9 flex items-center justify-center rounded-lg border border-ink-200
                                  text-ink-600 hover:bg-ink-50 text-sm transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="w-9 h-9 flex items-center justify-center rounded-lg border border-ink-200
                      text-ink-600 hover:bg-ink-50 hover:text-ink-900 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @else
            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-ink-300 cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        @endif
    </nav>
@endif