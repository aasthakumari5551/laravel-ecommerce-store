@auth
@php
    try {

        $notifUnread = auth()
            ->user()
            ->unreadNotifications()
            ->count();

    } catch (\Exception $e) {

        $notifUnread = 0;
    }
@endphp
<div x-data="notificationCenter({{ $notifUnread ?? 0 }})"
     class="relative">

    {{-- Bell --}}
    <button @click="toggle()" class="btn-ghost p-2.5 relative">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002
                     6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6
                     11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0
                     11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unread > 0"
              class="absolute top-1.5 right-1.5 w-4 h-4 bg-red-500 text-white
                     text-[9px] font-bold rounded-full flex items-center
                     justify-center animate-pulse">
            <span x-text="unread > 9 ? '9+' : unread"></span>
        </span>
    </button>

    {{-- Panel --}}
    <div x-show="open"
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 top-full mt-2 w-80 bg-white border border-ink-100
                rounded-2xl shadow-xl overflow-hidden z-50"
         x-cloak>

        <div class="flex items-center justify-between px-4 py-3 border-b border-ink-100">
            <p class="font-semibold text-ink-900 text-sm">Notifications</p>
            <button x-show="unread > 0" @click="markAllRead()"
                    class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                Mark all read
            </button>
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-ink-50">
            <template x-if="loading">
                <div class="p-4 space-y-3">
                    <template x-for="i in 3">
                        <div class="flex gap-3">
                            <div class="skeleton w-8 h-8 rounded-full flex-shrink-0"></div>
                            <div class="flex-1 space-y-1.5">
                                <div class="skeleton h-2.5 rounded w-3/4"></div>
                                <div class="skeleton h-2 rounded w-1/2"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-ink-400">No notifications yet</p>
                </div>
            </template>

            <template x-for="n in notifications" :key="n.id">
                <a :href="n.data.url || '#'"
                   @click="markRead(n.id)"
                   class="flex items-start gap-3 px-4 py-3 hover:bg-ink-50
                          transition-colors block"
                   :class="n.read_at ? 'opacity-70' : 'bg-brand-50/40'">
                    {{-- Icon --}}
                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                flex-shrink-0 mt-0.5"
                         :class="{
                             'bg-green-100': n.data.type === 'order_placed',
                             'bg-brand-100': n.data.type === 'low_stock',
                             'bg-blue-100':  true,
                         }">
                        <svg class="w-4 h-4"
                             :class="{
                                 'text-green-600': n.data.type === 'order_placed',
                                 'text-red-500':   n.data.type === 'low_stock',
                                 'text-blue-600':  !['order_placed','low_stock'].includes(n.data.type),
                             }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-ink-900 leading-snug"
                           x-text="n.data.message"></p>
                        <p class="text-xs text-ink-400 mt-1"
                           x-text="timeAgo(n.created_at)"></p>
                    </div>
                    <div x-show="!n.read_at"
                         class="w-2 h-2 bg-brand-500 rounded-full flex-shrink-0 mt-2">
                    </div>
                </a>
            </template>
        </div>

        <div class="px-4 py-2.5 border-t border-ink-100 bg-ink-50/50">
            <a href="{{ route('profile.edit') }}"
               class="text-xs text-brand-600 hover:text-brand-700 font-medium">
                View all in profile →
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function notificationCenter() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unread: {{ $notifUnread ?? 0 }},

        async toggle() {
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) await this.fetch();
        },

        async fetch() {
            this.loading = true;
            try {
                const res  = await fetch('/api/notifications',
                    { headers: { Accept: 'application/json' } });
                const data = await res.json();
                this.notifications = data.notifications || [];
                this.unread        = data.unread        || 0;
            } catch {}
            finally { this.loading = false; }
        },

        async markRead(id) {
            await fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    Accept: 'application/json',
                },
            });
            const n = this.notifications.find(n => n.id === id);
            if (n && !n.read_at) { n.read_at = new Date(); this.unread = Math.max(0, this.unread - 1); }
        },

        async markAllRead() {
            await fetch('/api/notifications/read-all', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    Accept: 'application/json',
                },
            });
            this.notifications.forEach(n => n.read_at = new Date());
            this.unread = 0;
        },

        timeAgo(dateStr) {
            const diff = Date.now() - new Date(dateStr);
            const m = Math.floor(diff / 60000);
            if (m < 1)  return 'Just now';
            if (m < 60) return `${m}m ago`;
            const h = Math.floor(m / 60);
            if (h < 24) return `${h}h ago`;
            return `${Math.floor(h / 24)}d ago`;
        }
    }
}
</script>
@endpush
@endauth