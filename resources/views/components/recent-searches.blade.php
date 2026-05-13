{{-- Drop into navbar search bar dropdown when query is empty --}}
<div x-data="recentSearchPanel()"
     x-show="recent.length > 0 && open && query.length < 2"
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 -translate-y-1"
     class="absolute top-full left-0 right-0 mt-2 bg-white border border-ink-100
            rounded-2xl shadow-xl overflow-hidden z-50 py-2"
     x-cloak>

    <div class="flex items-center justify-between px-4 py-1.5">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ink-400">
            Recent Searches
        </p>
        <button @click="clearAll()"
                class="text-[11px] text-ink-400 hover:text-red-500 transition-colors">
            Clear
        </button>
    </div>

    <template x-for="term in recent" :key="term">
        <div class="flex items-center gap-2 px-4 py-2 hover:bg-ink-50 transition-colors group">
            <svg class="w-3.5 h-3.5 text-ink-300 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <button class="flex-1 text-left text-sm text-ink-700 hover:text-ink-900"
                    @click="useSearch(term)" x-text="term">
            </button>
            <button @click.stop="remove(term)"
                    class="opacity-0 group-hover:opacity-100 text-ink-300
                           hover:text-ink-600 transition-all">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                </svg>
            </button>
        </div>
    </template>
</div>

@push('scripts')
<script>
function recentSearchPanel() {
    return {
        recent: window.recentSearches.get(),
        remove(term) {
            window.recentSearches.remove(term);
            this.recent = window.recentSearches.get();
        },
        clearAll() {
            window.recentSearches.clear();
            this.recent = [];
        },
        useSearch(term) {
            window.location.href = `/shop/products?q=${encodeURIComponent(term)}`;
        }
    }
}
</script>
@endpush