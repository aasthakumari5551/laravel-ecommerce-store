import './bootstrap';

// ── Global cart store (shared across components) ──────────
window.cartStore = () => ({
    count: parseInt(document.querySelector('[data-cart-count]')?.dataset.cartCount || '0'),
    subtotal: 0,
    open: false,

    increment(qty = 1) {
        this.count += qty;
        this.updateBadges();
    },
    decrement(qty = 1) {
        this.count = Math.max(0, this.count - qty);
        this.updateBadges();
    },
    updateBadges() {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.dataset.cartCount = this.count;
            el.textContent = this.count;
        });
    }
});

// ── Toast notification system ─────────────────────────────
window.toast = {
    _container: null,

    _ensure() {
        if (!this._container) {
            this._container = document.createElement('div');
            this._container.className =
                'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(this._container);
        }
    },

    show(message, type = 'success', duration = 3500) {
        this._ensure();

        const icons = {
            success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
            error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
            info:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
            cart:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>`,
            heart:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>`,
        };
        const colors = {
            success: 'bg-green-600', error: 'bg-red-600',
            info: 'bg-ink-800', cart: 'bg-brand-600', heart: 'bg-red-500',
        };

        const el = document.createElement('div');
        el.className = `pointer-events-auto flex items-center gap-3 ${colors[type] || colors.info}
                        text-white text-sm font-medium px-4 py-3 rounded-xl shadow-xl max-w-xs
                        translate-x-full opacity-0 transition-all duration-300`;
        el.innerHTML = `
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">${icons[type] || icons.info}</svg>
            <span>${message}</span>`;

        this._container.appendChild(el);
        requestAnimationFrame(() => {
            el.classList.remove('translate-x-full', 'opacity-0');
        });

        setTimeout(() => {
            el.classList.add('opacity-0', 'translate-x-full');
            setTimeout(() => el.remove(), 300);
        }, duration);
    },

    success: (msg, d) => window.toast.show(msg, 'success', d),
    error:   (msg, d) => window.toast.show(msg, 'error', d),
    cart:    (msg, d) => window.toast.show(msg, 'cart', d),
    heart:   (msg, d) => window.toast.show(msg, 'heart', d),
    info:    (msg, d) => window.toast.show(msg, 'info', d),
};

// ── Recent searches (localStorage) ───────────────────────
window.recentSearches = {
    key: 'velura_searches',
    get()          { try { return JSON.parse(localStorage.getItem(this.key) || '[]'); } catch { return []; } },
    add(term)      {
        if (!term?.trim()) return;
        const list = [term, ...this.get().filter(s => s !== term)].slice(0, 8);
        localStorage.setItem(this.key, JSON.stringify(list));
    },
    remove(term)   {
        localStorage.setItem(this.key, JSON.stringify(this.get().filter(s => s !== term)));
    },
    clear()        { localStorage.removeItem(this.key); },
};

// ── Intercept add-to-cart forms ───────────────────────────
document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!form.matches('[data-cart-form]')) return;
    e.preventDefault();

    const btn = form.querySelector('[type=submit]');
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor"
        viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>`;

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest',
                       'Accept': 'application/json' },
        });
        const data = await res.json();

        if (res.ok) {
            window.toast.cart('Added to cart!');
            // Success animation on button
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
            btn.classList.add('bg-green-600');
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('bg-green-600');
                btn.disabled = false;
            }, 1800);
        } else {
            window.toast.error(data.message || 'Could not add to cart');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    } catch {
        window.toast.error('Something went wrong');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
});

// ── Intercept wishlist toggle forms ──────────────────────
document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!form.matches('[data-wishlist-form]')) return;
    e.preventDefault();

    const btn = form.querySelector('button');
    btn.disabled = true;

    try {
        const res = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest',
                       'Accept': 'application/json' },
        });
        const data = await res.json();
        if (res.ok) {
            const added = data.added ?? true;
            window.toast.heart(added ? '❤️ Added to wishlist' : 'Removed from wishlist');
            // Toggle heart fill
            const svg = btn.querySelector('svg');
            if (svg) {
                svg.style.fill    = added ? '#ef4444' : 'none';
                svg.style.stroke  = added ? '#ef4444' : 'currentColor';
            }
        }
    } catch { window.toast.error('Something went wrong'); }
    finally  { btn.disabled = false; }
});