import './bootstrap';

// ── CSRF helper ───────────────────────────────────────────
const csrf = () => document
    .querySelector('meta[name=csrf-token]')?.content ?? '';

// ── Recent searches (localStorage) ───────────────────────
window.recentSearches = {
    _key: 'velura_searches',
    get()        { try { return JSON.parse(localStorage.getItem(this._key) ?? '[]'); } catch { return []; } },
    add(t)       { if (!t?.trim()) return; localStorage.setItem(this._key, JSON.stringify([t, ...this.get().filter(s => s !== t)].slice(0, 8))); },
    remove(t)    { localStorage.setItem(this._key, JSON.stringify(this.get().filter(s => s !== t))); },
    clear()      { localStorage.removeItem(this._key); },
};

// ── Toast system ──────────────────────────────────────────
window.toast = (() => {
    let container = null;

    const ensure = () => {
        if (container) return;
        container = Object.assign(document.createElement('div'), {
            className: 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none w-72',
        });
        document.body.appendChild(container);
    };

    const colors = {
        success: 'bg-green-600', error: 'bg-red-600',
        info: 'bg-ink-800', cart: 'bg-brand-600', heart: 'bg-red-500',
    };

    const svgPaths = {
        success: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        error:   'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        info:    'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        cart:    'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z',
        heart:   'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    };

    const show = (message, type = 'info', duration = 3500) => {
        ensure();
        const el = document.createElement('div');
        el.className = `pointer-events-auto flex items-center gap-3 ${colors[type] ?? colors.info}
            text-white text-sm font-medium px-4 py-3 rounded-xl shadow-xl max-w-full
            opacity-0 translate-x-4 transition-all duration-300`;
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.innerHTML = `
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="${svgPaths[type] ?? svgPaths.info}"/>
            </svg>
            <span class="flex-1 leading-snug">${message}</span>`;
        container.appendChild(el);
        requestAnimationFrame(() => {
            el.classList.remove('opacity-0', 'translate-x-4');
        });
        setTimeout(() => {
            el.classList.add('opacity-0', 'translate-x-4');
            setTimeout(() => el.remove(), 320);
        }, duration);
    };

    return {
        show,
        success: (m, d) => show(m, 'success', d),
        error:   (m, d) => show(m, 'error',   d),
        info:    (m, d) => show(m, 'info',     d),
        cart:    (m, d) => show(m, 'cart',     d),
        heart:   (m, d) => show(m, 'heart',    d),
    };
})();

// ── AJAX cart form intercept ──────────────────────────────
document.addEventListener('submit', async (e) => {
    const form = e.target.closest('[data-cart-form]');
    if (!form) return;
    e.preventDefault();
    e.stopPropagation();

    const btn = form.querySelector('[type=submit]');
    if (!btn || btn.disabled) return;

    const original = btn.innerHTML;
    btn.disabled  = true;
    btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor"
        viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
        stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11
        11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>`;

    try {
        const fd = new FormData(form);
        const res = await fetch(form.action, {
            method:  'POST',
            body:    fd,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     csrf(),
            },
        });
        const data = await res.json();

        if (res.ok) {
            window.toast.cart('Added to cart!');
            btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`;
            btn.classList.add('bg-green-600', 'border-green-600');

            // Update cart count badge
            if (data.cart_count !== undefined) {
                document.querySelectorAll('[data-cart-count]').forEach(el => {
                    el.textContent = data.cart_count;
                    el.dataset.cartCount = data.cart_count;
                    el.style.display = data.cart_count > 0 ? '' : 'none';
                });
            }

            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-green-600', 'border-green-600');
                btn.disabled  = false;
            }, 1800);
        } else {
            window.toast.error(data.message ?? 'Could not add to cart');
            btn.innerHTML = original;
            btn.disabled  = false;
        }
    } catch (err) {
        console.error('Cart error:', err);
        window.toast.error('Something went wrong. Please try again.');
        btn.innerHTML = original;
        btn.disabled  = false;
    }
}, true); // capture phase to catch before Alpine

// ── AJAX wishlist intercept ───────────────────────────────
document.addEventListener('submit', async (e) => {
    const form = e.target.closest('[data-wishlist-form]');
    if (!form) return;
    e.preventDefault();
    e.stopPropagation();

    const btn = form.querySelector('button[type=submit]');
    if (btn) btn.disabled = true;

    try {
        const res  = await fetch(form.action, {
            method:  'POST',
            body:    new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     csrf(),
            },
        });
        const data = await res.json();

        if (res.ok) {
            const added = data.added ?? true;
            window.toast.heart(added ? '❤️ Saved to wishlist' : 'Removed from wishlist');
            if (btn) {
                const svg   = btn.querySelector('svg');
                if (svg) {
                    svg.style.fill   = added ? '#ef4444' : 'none';
                    svg.style.color  = added ? '#ef4444' : '';
                }
            }
        } else {
            window.toast.error(data.message ?? 'Please sign in first');
        }
    } catch {
        window.toast.error('Something went wrong');
    } finally {
        if (btn) btn.disabled = false;
    }
}, true);

// ── Lazy image fade-in ────────────────────────────────────
const initLazyImages = (root = document) => {
    root.querySelectorAll('img[loading="lazy"]').forEach(img => {
        const done = () => img.classList.add('loaded');
        img.complete ? done() : img.addEventListener('load', done);
        img.addEventListener('error', done);
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initLazyImages();

    // Watch for dynamically added images
    new MutationObserver(mutations => {
        mutations.forEach(m =>
            m.addedNodes.forEach(node => {
                if (node.nodeType !== 1) return;
                if (node.tagName === 'IMG') {
                    const done = () => node.classList.add('loaded');
                    node.complete ? done() : node.addEventListener('load', done);
                } else {
                    initLazyImages(node);
                }
            })
        );
    }).observe(document.body, { childList: true, subtree: true });
});

// ── Cart drawer open trigger ──────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-open-cart]').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('cart-open'));
        });
    });
});