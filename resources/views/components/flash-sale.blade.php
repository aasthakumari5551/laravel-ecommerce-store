@props(['products', 'endsAt' => null])

@if($products->isNotEmpty())
<section class="bg-gradient-to-r from-red-600 via-red-500 to-orange-500
                rounded-2xl overflow-hidden my-6" x-data="flashTimer()">

    {{-- Header --}}
    <div class="flex items-center justify-between px-5 sm:px-7 pt-5 pb-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">⚡</span>
            <div>
                <p class="font-display text-xl text-white font-bold leading-tight">
                    Flash Sale
                </p>
                <p class="text-red-100 text-xs">Limited time · Limited stock</p>
            </div>
        </div>
        {{-- Countdown --}}
        <div class="flex items-center gap-1.5" x-show="!expired">
            <span class="text-red-100 text-xs font-medium mr-1">Ends in</span>
            @foreach(['h','m','s'] as $unit)
                <div class="bg-white/20 backdrop-blur-sm rounded-lg px-2.5 py-1.5 min-w-[40px]
                             text-center">
                    <p class="text-white font-bold text-sm leading-none font-mono"
                       x-text="{{ $unit === 'h' ? 'hours' : ($unit === 'm' ? 'minutes' : 'seconds') }}">
                        --
                    </p>
                    <p class="text-red-200 text-[9px] uppercase mt-0.5">{{ $unit }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Products --}}
    <div class="flex gap-3 overflow-x-auto scrollbar-none px-5 sm:px-7 pb-5 snap-x">
        @foreach($products as $product)
            <a href="{{ route('shop.products.show', $product) }}"
               class="flex-shrink-0 w-36 sm:w-44 bg-white rounded-xl overflow-hidden
                      group snap-start hover:shadow-lg transition-shadow duration-200">
                <div class="relative aspect-square bg-ink-50 overflow-hidden">
                    @if($product->primaryImage)
                        <img src="{{ $product->primaryImage->url }}"
                             alt="{{ $product->name }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105
                                    transition-transform duration-400">
                    @endif
                    <div class="absolute top-2 left-2">
                        <span class="badge bg-red-500 text-white text-[10px] px-2 py-0.5 font-bold">
                            −{{ $product->discountPercentage() }}%
                        </span>
                    </div>
                </div>
                <div class="p-2.5">
                    <p class="text-xs font-medium text-ink-900 line-clamp-2 leading-snug mb-1.5">
                        {{ $product->name }}
                    </p>
                    <p class="text-sm font-bold text-ink-900">
                        ₹{{ number_format($product->price, 0) }}
                    </p>
                    <p class="text-[11px] text-ink-400 line-through">
                        ₹{{ number_format($product->compare_price, 0) }}
                    </p>
                    {{-- Stock urgency --}}
                    @if($product->stock <= 20)
                        <div class="mt-2">
                            <div class="flex justify-between text-[10px] text-red-500 mb-0.5">
                                <span>{{ $product->stock }} left</span>
                                <span>{{ round((1 - $product->stock/50) * 100) }}% sold</span>
                            </div>
                            <div class="w-full bg-red-100 rounded-full h-1">
                                <div class="bg-red-500 h-1 rounded-full transition-all"
                                     style="width:{{ min(95, round((1 - $product->stock/50)*100)) }}%">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
function flashTimer() {
    // 4-hour rolling flash sale window
    const end = new Date();
    end.setHours(end.getHours() + 4 - (end.getHours() % 4), 0, 0, 0);

    return {
        hours: '00', minutes: '00', seconds: '00', expired: false,
        init() {
            this.tick();
            setInterval(() => this.tick(), 1000);
        },
        tick() {
            const diff = end - Date.now();
            if (diff <= 0) { this.expired = true; return; }
            this.hours   = String(Math.floor(diff / 3600000)).padStart(2,'0');
            this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2,'0');
            this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2,'0');
        }
    }
}
</script>
@endpush
@endif