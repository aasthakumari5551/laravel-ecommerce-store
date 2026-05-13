@props(['brands'])

@if($brands->isNotEmpty())
<section>
    <x-section-header title="Shop by Brand" subtitle="Trusted names, quality guaranteed" />

    <div class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($brands->take(8) as $brand)
            <a href="{{ route('shop.products.index', ['q' => $brand->brand]) }}"
               class="card px-3 py-4 flex flex-col items-center gap-2 text-center group
                      hover:border-brand-300 hover:shadow-card-hover transition-all duration-200">

                {{-- Brand monogram --}}
                <div class="w-12 h-12 rounded-xl bg-ink-50 group-hover:bg-brand-50
                             flex items-center justify-center transition-colors flex-shrink-0">
                    <span class="text-lg font-display font-bold text-ink-400
                                 group-hover:text-brand-600 transition-colors">
                        {{ strtoupper(substr($brand->brand, 0, 1)) }}
                    </span>
                </div>

                <div>
                    <p class="text-xs font-semibold text-ink-900 leading-tight
                               group-hover:text-brand-700 transition-colors">
                        {{ $brand->brand }}
                    </p>
                    <p class="text-[10px] text-ink-400 mt-0.5">
                        {{ $brand->product_count }} products
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif