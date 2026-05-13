@props(['product'])

<div x-data="shareProduct()" class="relative">
    <button @click="toggle()"
            class="btn-ghost text-sm gap-2 px-3 py-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
        </svg>
        Share
    </button>

    <div x-show="open" @click.outside="open = false"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         class="absolute bottom-full left-0 mb-2 bg-white border border-ink-100
                rounded-2xl shadow-xl overflow-hidden z-20 w-52 py-1.5"
         x-cloak>

        @php
            $url    = route('shop.products.show', $product);
            $title  = urlencode($product->name);
            $shares = [
                ['label' => 'Copy Link',  'action' => 'copy',   'color' => 'text-ink-700',
                 'icon' => 'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3'],
                ['label' => 'WhatsApp',   'action' => "window.open('https://wa.me/?text={$title}%20{$url}')", 'color' => 'text-green-600',
                 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ['label' => 'Twitter',    'action' => "window.open('https://twitter.com/intent/tweet?text={$title}&url={$url}')", 'color' => 'text-sky-500',
                 'icon' => 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z'],
            ];
        @endphp

        @foreach($shares as $s)
            <button @click="{{ $s['action'] === 'copy' ? 'copyLink()' : $s['action'] }}"
                    class="flex items-center gap-3 px-4 py-2 w-full text-sm font-medium
                           {{ $s['color'] }} hover:bg-ink-50 transition-colors text-left">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                     stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                </svg>
                <span x-text="'{{ $s['label'] }}' === 'Copy Link' ? copyLabel : '{{ $s['label'] }}'">
                    {{ $s['label'] }}
                </span>
            </button>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
function shareProduct() {
    return {
        open: false,
        copyLabel: 'Copy Link',
        toggle() { this.open = !this.open; },
        async copyLink() {
            try {
                await navigator.clipboard.writeText(window.location.href);
                this.copyLabel = 'Copied!';
                window.toast.success('Link copied to clipboard');
                setTimeout(() => { this.copyLabel = 'Copy Link'; }, 2000);
            } catch {
                window.toast.error('Could not copy link');
            }
            this.open = false;
        }
    }
}
</script>
@endpush