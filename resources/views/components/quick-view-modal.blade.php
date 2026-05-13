<div x-data="quickView()"
     @quick-view.window="open($event.detail)"
     @keydown.escape.window="close()">

    {{-- Backdrop --}}
    <div x-show="show"
         @click="close()"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:leave="transition duration-150"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-ink-900/60 backdrop-blur-sm z-[80]"
         x-cloak>
    </div>

    {{-- Modal --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="fixed inset-x-4 top-[50%] -translate-y-[50%] max-w-3xl mx-auto
                bg-white rounded-2xl shadow-2xl z-[90] overflow-hidden
                max-h-[85vh] overflow-y-auto"
         x-cloak>

        {{-- Loading state --}}
        <template x-if="loading">
            <div class="grid grid-cols-2 gap-0 min-h-[400px]">
                <div class="skeleton aspect-square"></div>
                <div class="p-6 space-y-4">
                    <div class="skeleton h-4 rounded w-1/3"></div>
                    <div class="skeleton h-7 rounded w-full"></div>
                    <div class="skeleton h-4 rounded w-2/3"></div>
                    <div class="skeleton h-10 rounded w-1/3 mt-4"></div>
                    <div class="skeleton h-11 rounded w-full mt-4"></div>
                </div>
            </div>
        </template>

        {{-- Product content --}}
        <template x-if="!loading && product">
            <div class="grid grid-cols-1 sm:grid-cols-2">

                {{-- Image --}}
                <div class="relative bg-ink-50 aspect-square sm:aspect-auto min-h-[240px]">
                    <img :src="product.primary_image_url || '/images/placeholder.svg'"
                         :alt="product.name"
                         class="w-full h-full object-cover">
                    <button @click="close()"
                            class="absolute top-3 right-3 w-8 h-8 bg-white/90 rounded-full
                                   flex items-center justify-center shadow hover:bg-white
                                   transition-colors sm:hidden">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Info --}}
                <div class="p-6 flex flex-col" x-data="{ qty: 1 }">
                    <div class="flex items-start justify-between">
                        <p class="text-xs font-semibold uppercase tracking-widest
                                   text-brand-600 mb-1" x-text="product.category?.name || ''">
                        </p>
                        <button @click="close()"
                                class="btn-ghost p-1.5 -mt-1 -mr-1.5 hidden sm:flex">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <h2 class="font-display text-xl text-ink-900 leading-tight mb-2"
                        x-text="product.name">
                    </h2>

                    {{-- Stars --}}
                    <div x-show="product.review_count > 0"
                         class="flex items-center gap-2 mb-3">
                        <div class="flex gap-0.5">
                            <template x-for="i in 5" :key="i">
                                <svg class="w-3.5 h-3.5"
                                     :class="i <= Math.round(product.avg_rating)
                                         ? 'text-brand-500' : 'text-ink-200'"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </template>
                        </div>
                        <span class="text-xs text-ink-400"
                              x-text="'(' + product.review_count + ')'"></span>
                    </div>

                    {{-- Price --}}
                    <div class="flex items-baseline gap-3 mb-4">
                        <span class="text-2xl font-bold text-ink-900"
                              x-text="'₹' + parseFloat(product.price).toLocaleString('en-IN')">
                        </span>
                        <template x-if="product.compare_price">
                            <span class="text-sm text-ink-400 line-through"
                                  x-text="'₹' + parseFloat(product.compare_price).toLocaleString('en-IN')">
                            </span>
                        </template>
                    </div>

                    <p class="text-sm text-ink-600 leading-relaxed mb-5 flex-1"
                       x-text="product.short_description || ''">
                    </p>

                    {{-- Qty --}}
                    <div class="flex items-center gap-3 mb-3">
                        <div class="flex items-center border border-ink-200 rounded-xl overflow-hidden">
                            <button @click="qty = Math.max(1, qty - 1)"
                                    class="w-9 h-9 flex items-center justify-center
                                           text-ink-500 hover:bg-ink-50 text-lg font-medium">
                                −
                            </button>
                            <span class="w-8 text-center text-sm font-semibold"
                                  x-text="qty"></span>
                            <button @click="qty++"
                                    class="w-9 h-9 flex items-center justify-center
                                           text-ink-500 hover:bg-ink-50 text-lg font-medium">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="space-y-2">
                        <form :action="'/cart'" method="POST" data-cart-form>
                            <input type="hidden" name="_token"
                                   :value="csrfToken">
                            <input type="hidden" name="product_id"
                                   :value="product.id">
                            <input type="hidden" name="quantity" :value="qty">
                            <button type="submit"
                                    class="btn-primary w-full justify-center py-3 rounded-xl">
                                Add to Cart
                            </button>
                        </form>
                        <a :href="'/shop/products/' + product.uuid"
                           class="btn-secondary w-full justify-center text-sm py-2.5 rounded-xl">
                            View Full Details →
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function quickView() {
    return {
        show: false,
        loading: false,
        product: null,
        csrfToken: document.querySelector('meta[name=csrf-token]')?.content || '',

        async open(uuid) {
            this.show    = true;
            this.loading = true;
            this.product = null;
            document.body.style.overflow = 'hidden';
            try {
                const res    = await fetch(`/api/v1/products/${uuid}`,
                    { headers: { Accept: 'application/json' } });
                const data   = await res.json();
                this.product = data.data || data;
            } catch { window.toast.error('Could not load product'); this.show = false; }
            finally  { this.loading = false; }
        },

        close() {
            this.show = false;
            document.body.style.overflow = '';
        }
    }
}
</script>
@endpush