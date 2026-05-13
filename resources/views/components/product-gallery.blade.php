@props(['product'])

<div x-data="productGallery()" class="space-y-3">

    {{-- Main image --}}
    <div class="relative overflow-hidden rounded-2xl bg-ink-50 aspect-square
                border border-ink-100 select-none"
         @touchstart="touchStart($event)"
         @touchend="touchEnd($event)">

        <template x-for="(img, i) in images" :key="i">
            <div x-show="activeIndex === i"
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0"
                 class="absolute inset-0">
                <img :src="img.url"
                     :alt="img.alt"
                     class="w-full h-full object-cover transition-transform duration-300"
                     :class="zoomed ? 'scale-[2] cursor-zoom-out' : 'cursor-zoom-in'"
                     :style="zoomed ? `transform-origin: ${zoomX}% ${zoomY}%` : ''"
                     @click="toggleZoom($event)"
                     @mousemove="zoomed && trackMouse($event)"
                     loading="eager">
            </div>
        </template>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
            @if($product->isOnSale())
                <span class="badge bg-red-500 text-white text-xs px-2.5 py-1 shadow">
                    −{{ $product->discountPercentage() }}%
                </span>
            @endif
            @if($product->is_featured)
                <span class="badge bg-brand-500 text-white text-xs px-2.5 py-1 shadow">
                    Featured
                </span>
            @endif
        </div>

        {{-- Swipe arrows (desktop) --}}
        <template x-if="images.length > 1">
            <div>
                <button @click="prev()"
                        class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8
                               bg-white/90 backdrop-blur-sm rounded-full shadow
                               flex items-center justify-center text-ink-600
                               hover:bg-white transition-all opacity-0 hover:opacity-100
                               group-hover:opacity-100 z-10"
                        :class="images.length > 1 ? 'opacity-70 hover:opacity-100' : 'hidden'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click="next()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8
                               bg-white/90 backdrop-blur-sm rounded-full shadow
                               flex items-center justify-center text-ink-600
                               hover:bg-white transition-all z-10"
                        :class="images.length > 1 ? 'opacity-70 hover:opacity-100' : 'hidden'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </template>

        {{-- Dot indicators (mobile) --}}
        <template x-if="images.length > 1">
            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
                <template x-for="(img, i) in images" :key="i">
                    <button @click="activeIndex = i"
                            class="rounded-full transition-all duration-200"
                            :class="activeIndex === i
                                ? 'w-4 h-1.5 bg-white'
                                : 'w-1.5 h-1.5 bg-white/50 hover:bg-white/80'">
                    </button>
                </template>
            </div>
        </template>

        {{-- Zoom hint --}}
        <div class="absolute bottom-3 right-3 bg-black/40 backdrop-blur-sm text-white
                     text-[10px] rounded-lg px-2 py-1 pointer-events-none
                     opacity-0 hover:opacity-100 transition-opacity hidden sm:block"
             x-show="!zoomed">
            Click to zoom
        </div>
    </div>

    {{-- Thumbnails --}}
    <template x-if="images.length > 1">
        <div class="flex gap-2 overflow-x-auto scrollbar-none pb-1">
            <template x-for="(img, i) in images" :key="i">
                <button @click="activeIndex = i; zoomed = false"
                        class="flex-shrink-0 w-16 h-16 sm:w-18 sm:h-18 rounded-xl
                               overflow-hidden border-2 transition-all duration-200"
                        :class="activeIndex === i
                            ? 'border-brand-500 ring-2 ring-brand-200 scale-105'
                            : 'border-ink-100 hover:border-ink-300'">
                    <img :src="img.url" :alt="img.alt"
                         class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </template>
</div>

@push('scripts')
<script>
function productGallery() {
    const rawImages = @json($product->images->map(fn($img) => [
        'url' => $img->url,
        'alt' => $img->alt_text ?? $product->name,
    ]));

    // Fallback if no images
    const images = rawImages.length ? rawImages : [
        { url: '/images/placeholder.png', alt: @json($product->name) }
    ];

    return {
        images,
        activeIndex: 0,
        zoomed: false,
        zoomX: 50,
        zoomY: 50,
        _touchStartX: null,

        prev() {
            this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
            this.zoomed = false;
        },
        next() {
            this.activeIndex = (this.activeIndex + 1) % this.images.length;
            this.zoomed = false;
        },
        toggleZoom(e) {
            this.zoomed = !this.zoomed;
            if (this.zoomed) this.trackMouse(e);
        },
        trackMouse(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            this.zoomX = ((e.clientX - rect.left) / rect.width)  * 100;
            this.zoomY = ((e.clientY - rect.top)  / rect.height) * 100;
        },
        touchStart(e) {
            this._touchStartX = e.touches[0].clientX;
        },
        touchEnd(e) {
            if (this._touchStartX === null) return;
            const diff = this._touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? this.next() : this.prev();
            }
            this._touchStartX = null;
        },
    }
}
</script>
@endpush