@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <h1 class="font-display text-3xl text-ink-900 mb-7">My Account</h1>

    {{-- Stats strip --}}
    <div class="grid grid-cols-3 gap-3 mb-7">
        @foreach([
            ['label' => 'Total Orders', 'value' => $orderCount,  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Reviews',      'value' => $reviewCount, 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
            ['label' => 'Member Since', 'value' => auth()->user()->created_at->format('M Y'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ] as $stat)
            <div class="card p-4 text-center">
                <svg class="w-5 h-5 text-brand-500 mx-auto mb-1.5" fill="none" stroke="currentColor"
                     stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                </svg>
                <p class="font-bold text-ink-900 text-lg leading-none">{{ $stat['value'] }}</p>
                <p class="text-ink-400 text-xs mt-0.5">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6"
         x-data="{ tab: 'profile' }">

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="card p-5">
                {{-- Avatar --}}
                <div class="text-center mb-5 pb-5 border-b border-ink-100">
                    <div class="relative inline-block">
                        <img src="{{ $avatarUrl }}"
                             alt="{{ $user->name }}"
                             class="w-20 h-20 rounded-2xl object-cover mx-auto shadow-md">
                        <label class="absolute -bottom-2 -right-2 w-7 h-7 bg-brand-600 hover:bg-brand-700
                                      rounded-lg flex items-center justify-center cursor-pointer
                                      transition-colors shadow">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <input type="file" class="sr-only" accept="image/*"
                                   onchange="this.closest('form') || document.getElementById('avatar-form').submit()">
                        </label>
                    </div>
                    <p class="font-semibold text-ink-900 mt-3 text-sm">{{ $user->name }}</p>
                    <p class="text-ink-400 text-xs">{{ $user->email }}</p>
                </div>

                {{-- Nav --}}
                <nav class="space-y-1">
                    @foreach([
                        ['key' => 'profile',   'label' => 'Profile Info',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['key' => 'password',  'label' => 'Change Password', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                        ['key' => 'prefs',     'label' => 'Notifications',   'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                    ] as $item)
                        <button @click="tab = '{{ $item['key'] }}'"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm
                                       font-medium transition-all text-left"
                                :class="tab === '{{ $item['key'] }}'
                                    ? 'bg-brand-50 text-brand-700'
                                    : 'text-ink-600 hover:bg-ink-50 hover:text-ink-900'">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor"
                                 stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </button>
                    @endforeach
                </nav>

                <div class="mt-4 pt-4 border-t border-ink-100 space-y-2">
                    <a href="{{ route('orders.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                              text-ink-600 hover:bg-ink-50 hover:text-ink-900 transition-colors font-medium">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor"
                             stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        My Orders
                    </a>
                    <a href="{{ route('addresses.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                              text-ink-600 hover:bg-ink-50 hover:text-ink-900 transition-colors font-medium">
                        <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor"
                             stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        Saved Addresses
                    </a>
                </div>
            </div>
        </div>

        {{-- Content panels --}}
        <div class="lg:col-span-2">

            {{-- Profile form --}}
            <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1">
                <div class="card p-6">
                    <h2 class="font-semibold text-ink-900 mb-5">Profile Information</h2>
                    @if(session('success'))
                        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
                    @endif
                    <form method="POST" action="{{ route('profile.update') }}"
                          enctype="multipart/form-data">
                        @csrf @method('PATCH')
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-ink-700 mb-1.5">
                                        Full Name
                                    </label>
                                    <input type="text" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           class="input @error('name') ring-2 ring-red-300 @enderror">
                                    @error('name')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-ink-700 mb-1.5">
                                        Phone
                                    </label>
                                    <input type="tel" name="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="+91 00000 00000"
                                           class="input">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink-700 mb-1.5">
                                    Email
                                </label>
                                <input type="email" value="{{ $user->email }}"
                                       class="input bg-ink-50 cursor-not-allowed opacity-70"
                                       disabled>
                                <p class="text-xs text-ink-400 mt-1">
                                    Contact support to change your email.
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ink-700 mb-1.5">
                                    Bio
                                    <span class="text-ink-400 font-normal">(optional)</span>
                                </label>
                                <textarea name="bio" rows="3"
                                          placeholder="Tell us a little about yourself…"
                                          class="input resize-none">{{ old('bio', $user->bio) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-5 flex gap-3">
                            <button type="submit" class="btn-primary">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Password --}}
            <div x-show="tab === 'password'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1" x-cloak>
                <div class="card p-6">
                    <h2 class="font-semibold text-ink-900 mb-5">Change Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PATCH')
                        <div class="space-y-4">
                            @foreach([
                                ['name' => 'current_password', 'label' => 'Current Password'],
                                ['name' => 'password',         'label' => 'New Password'],
                                ['name' => 'password_confirmation', 'label' => 'Confirm New Password'],
                            ] as $field)
                                <div x-data="{ show: false }">
                                    <label class="block text-sm font-medium text-ink-700 mb-1.5">
                                        {{ $field['label'] }}
                                    </label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'"
                                               name="{{ $field['name'] }}"
                                               class="input pr-10
                                                      @error($field['name']) ring-2 ring-red-300 @enderror">
                                        <button type="button" @click="show = !show"
                                                class="absolute right-3 top-2.5 text-ink-400
                                                       hover:text-ink-700 transition-colors">
                                            <svg x-show="!show" class="w-4 h-4" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="show" class="w-4 h-4" fill="none"
                                                 stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error($field['name'])
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn-primary mt-5">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Notifications prefs --}}
            <div x-show="tab === 'prefs'" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1" x-cloak>
                <div class="card p-6">
                    <h2 class="font-semibold text-ink-900 mb-5">Notification Preferences</h2>
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PATCH')
                        @php
                            $prefs = $user->notification_preferences ?? [];
                        @endphp
                        <div class="space-y-4">
                            @foreach([
                                ['key' => 'order_updates', 'label' => 'Order Updates',
                                 'desc' => 'Status changes, shipping, delivery confirmations'],
                                ['key' => 'promotions',    'label' => 'Promotions & Offers',
                                 'desc' => 'Sale alerts, coupons, and exclusive deals'],
                            ] as $pref)
                                <div class="flex items-start justify-between gap-4 py-3
                                            border-b border-ink-100 last:border-0">
                                    <div>
                                        <p class="text-sm font-medium text-ink-900">
                                            {{ $pref['label'] }}
                                        </p>
                                        <p class="text-xs text-ink-400 mt-0.5">{{ $pref['desc'] }}</p>
                                    </div>
                                    <label class="relative inline-flex items-center flex-shrink-0 cursor-pointer">
                                        <input type="checkbox"
                                               name="notification_preferences[{{ $pref['key'] }}]"
                                               value="1"
                                               {{ ($prefs[$pref['key']] ?? false) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-ink-200 rounded-full peer
                                                    peer-checked:bg-brand-600 transition-colors
                                                    after:content-[''] after:absolute after:top-0.5
                                                    after:left-0.5 after:bg-white after:rounded-full
                                                    after:h-5 after:w-5 after:transition-all
                                                    peer-checked:after:translate-x-5">
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn-primary mt-5">
                            Save Preferences
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection