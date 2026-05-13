<div class="border border-ink-200 rounded-xl p-4" x-data="pincodeChecker()">
    <h4 class="text-sm font-semibold text-ink-900 mb-3 flex items-center gap-2">
        <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
        </svg>
        Check Delivery
    </h4>

    <div class="flex gap-2">
        <input type="text" x-model="pincode" maxlength="6"
               @keydown.enter="check()"
               placeholder="Enter pincode"
               class="input text-sm flex-1 font-mono tracking-widest"
               :class="status === 'error' ? 'ring-2 ring-red-300 border-red-300' : ''">
        <button @click="check()"
                :disabled="loading || pincode.length !== 6"
                class="btn-primary text-sm flex-shrink-0 px-4 disabled:opacity-50
                       disabled:cursor-not-allowed">
            <span x-show="!loading">Check</span>
            <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </button>
    </div>

    <div x-show="result" x-transition class="mt-3 text-sm space-y-2" x-cloak>
        <template x-if="status === 'success'">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2 text-green-700">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"/>
                    </svg>
                    <span class="font-medium" x-text="result.area"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-1">
                    <div class="bg-green-50 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-green-600 uppercase font-semibold tracking-wide">
                            Standard
                        </p>
                        <p class="text-sm font-bold text-green-800 mt-0.5" x-text="result.standard"></p>
                        <p class="text-[11px] text-green-600">Free delivery</p>
                    </div>
                    <div class="bg-brand-50 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-brand-600 uppercase font-semibold tracking-wide">
                            Express
                        </p>
                        <p class="text-sm font-bold text-brand-800 mt-0.5" x-text="result.express"></p>
                        <p class="text-[11px] text-brand-600">₹49 extra</p>
                    </div>
                </div>
            </div>
        </template>
        <template x-if="status === 'error'">
            <div class="flex items-center gap-2 text-red-600">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm" x-text="result.message"></span>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
function pincodeChecker() {
    return {
        pincode: '',
        loading: false,
        status: null,
        result: null,

        async check() {
            if (this.pincode.length !== 6) return;
            this.loading = true;
            this.result  = null;

            // Simulate API — replace with real postal API
            await new Promise(r => setTimeout(r, 600));

            const valid = /^[1-9][0-9]{5}$/.test(this.pincode);
            const metro = ['110', '400', '560', '600', '700', '500', '380'];
            const isMetro = metro.some(p => this.pincode.startsWith(p));

            if (valid) {
                const today    = new Date();
                const std      = new Date(today);
                const express  = new Date(today);
                std.setDate(std.getDate() + (isMetro ? 2 : 4));
                express.setDate(express.getDate() + 1);
                const fmt = d => d.toLocaleDateString('en-IN',
                    { weekday:'short', day:'numeric', month:'short' });
                this.status = 'success';
                this.result = {
                    area:     isMetro ? 'Metro delivery available' : 'Delivery available',
                    standard: fmt(std),
                    express:  fmt(express),
                };
            } else {
                this.status = 'error';
                this.result = { message: 'Delivery not available at this pincode.' };
            }
            this.loading = false;
        }
    }
}
</script>
@endpush