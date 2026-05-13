<div x-data="compareBar()"
     @compare-toggle.window="toggle($event.detail)"
     x-show="items.length > 0"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="translate-y-full"
     x-transition:enter-end="translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-end="translate-y-full"
     class="fixed bottom-0 inset-x-0 z-50 bg-white border-t border-ink-200
            shadow-2xl px-4 py-3"
     x-cloak>
    <div class="max-w-5xl mx-auto flex items-center gap-4">

        <p class="text-sm font-semibold text-ink-800 flex-shrink-0">
            Compare (<span x-text="items.length"></span>/3)
        </p>

        <div class="flex gap-3 flex-1 overflow-x-auto scrollbar-none">
            <template x-for="item in items" :key="item.uuid">
                <div class="flex items-center gap-2 bg-ink-50 border border-ink-200
                             rounded-xl px-3 py-1.5 flex-shrink-0">
                    <img :src="item.image" class="w-8 h-8 rounded-lg object-cover">
                    <span class="text-xs font-medium text-ink-800 max-w-[80px] truncate"
                          x-text="item.name">
                    </span>
                    <button @click="remove(item.uuid)"
                            class="text-ink-400 hover:text-red-500 transition-colors ml-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                        </svg>
                    </button>
                </div>
            </template>

            {{-- Empty slots --}}
            <template x-for="i in (3 - items.length)" :key="'empty-' + i">
                <div class="flex items-center justify-center w-24 h-10 border-2
                             border-dashed border-ink-200 rounded-xl flex-shrink-0">
                    <span class="text-xs text-ink-300">+ Add</span>
                </div>
            </template>
        </div>

        <div class="flex gap-2 flex-shrink-0">
            <button @click="compare()"
                    :disabled="items.length < 2"
                    class="btn-primary text-sm px-4 py-2 disabled:opacity-40
                           disabled:cursor-not-allowed">
                Compare
            </button>
            <button @click="clear()"
                    class="btn-ghost text-sm px-3 py-2 text-red-500 hover:bg-red-50">
                Clear
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function compareBar() {
    return {
        items: JSON.parse(localStorage.getItem('velura_compare') || '[]'),

        save() { localStorage.setItem('velura_compare', JSON.stringify(this.items)); },

        toggle(product) {
            const idx = this.items.findIndex(i => i.uuid === product.uuid);
            if (idx > -1) {
                this.items.splice(idx, 1);
                window.toast.info('Removed from compare');
            } else if (this.items.length >= 3) {
                window.toast.error('You can compare up to 3 products');
                return;
            } else {
                this.items.push(product);
                window.toast.info('Added to compare');
            }
            this.save();
        },

        remove(uuid) {
            this.items = this.items.filter(i => i.uuid !== uuid);
            this.save();
        },

        clear() { this.items = []; this.save(); },

        compare() {
            const ids = this.items.map(i => i.uuid).join(',');
            window.location.href = `/shop/compare?ids=${ids}`;
        }
    }
}
</script>
@endpush