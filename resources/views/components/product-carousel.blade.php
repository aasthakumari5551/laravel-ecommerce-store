@props([
    'products',
    'title'      => null,
    'subtitle'   => null,
    'link'       => null,
    'linkLabel'  => 'View All',
    'id'         => 'carousel-' . uniqid(),
    'badge'      => null,      // e.g. 'SALE' | 'NEW' | 'HOT'
    'badgeColor' => 'brand',
])

@if($products->isNotEmpty())
<section class="overflow-hidden">
    @if($title)
        <div class="flex items-end justify-between mb-5 px-4 md:px-0">
            <div>
                <div class="flex items-center gap-2.5 mb-1">
                    @if($badge)
                        <span class="badge bg-{{ $badgeColor }}-600 text-white text-[10px]
                                     px-2.5 py-1 tracking-wider">
                            {{ $badge }}
                        </span>
                    @endif
                    <h2 class="section-title">{{ $title }}</h2>
                </div>
                @if($subtitle)
                    <p class="text-sm text-ink-400">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <button onclick="scrollCarousel('{{ $id }}', -1)"
                        class="w-8 h-8 rounded-full border border-ink-200 bg-white
                               flex items-center justify-center text-ink-500
                               hover:border-brand-400 hover:text-brand-600 transition-all
                               shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button onclick="scrollCarousel('{{ $id }}', 1)"
                        class="w-8 h-8 rounded-full border border-ink-200 bg-white
                               flex items-center justify-center text-ink-500
                               hover:border-brand-400 hover:text-brand-600 transition-all
                               shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                @if($link)
                    <a href="{{ $link }}"
                       class="text-sm font-medium text-brand-700 hover:text-brand-800
                              transition-colors flex items-center gap-1 group ml-1">
                        {{ $linkLabel }}
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div id="{{ $id }}"
         class="flex gap-3 overflow-x-auto scrollbar-none pb-2 px-4 md:px-0
                scroll-smooth snap-x snap-mandatory">
        @foreach($products as $product)
            <div class="flex-shrink-0 w-44 xs:w-48 sm:w-52 snap-start">
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>
</section>

@once
@push('scripts')
<script>
function scrollCarousel(id, dir) {
    const el = document.getElementById(id);
    if (!el) return;
    el.scrollBy({ left: dir * 220, behavior: 'smooth' });
}
</script>
@endpush
@endonce
@endif