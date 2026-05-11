@extends('layouts.app')
@section('title', 'My Addresses')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8" x-data="{ showForm: false }">

    <div class="flex items-center justify-between mb-7">
        <h1 class="font-display text-3xl text-ink-900">Addresses</h1>
        <button @click="showForm = !showForm" class="btn-primary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Add Address
        </button>
    </div>

    @if(session('success'))
        <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
    @endif

    {{-- Add form --}}
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         class="card p-6 mb-5" x-cloak>
        <h2 class="font-semibold text-ink-900 mb-5">New Address</h2>
        <form method="POST" action="{{ route('addresses.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                @foreach([
                    ['name' => 'first_name', 'label' => 'First Name',   'type' => 'text',  'span' => false],
                    ['name' => 'last_name',  'label' => 'Last Name',    'type' => 'text',  'span' => false],
                    ['name' => 'phone',      'label' => 'Phone',        'type' => 'tel',   'span' => false],
                    ['name' => 'label',      'label' => 'Label',        'type' => 'text',  'span' => false, 'placeholder' => 'Home, Work…'],
                    ['name' => 'line1',      'label' => 'Address Line 1','type' => 'text', 'span' => true],
                    ['name' => 'line2',      'label' => 'Address Line 2','type' => 'text', 'span' => true, 'optional' => true],
                    ['name' => 'city',       'label' => 'City',         'type' => 'text',  'span' => false],
                    ['name' => 'state',      'label' => 'State',        'type' => 'text',  'span' => false],
                    ['name' => 'pincode',    'label' => 'Pincode',      'type' => 'text',  'span' => false],
                ] as $f)
                    <div class="{{ ($f['span'] ?? false) ? 'sm:col-span-2' : '' }}">
                        <label class="block text-sm font-medium text-ink-700 mb-1.5">
                            {{ $f['label'] }}
                            @if($f['optional'] ?? false)
                                <span class="text-ink-400 font-normal">(optional)</span>
                            @endif
                        </label>
                        <input type="{{ $f['type'] }}" name="{{ $f['name'] }}"
                               value="{{ old($f['name']) }}"
                               placeholder="{{ $f['placeholder'] ?? '' }}"
                               class="input text-sm @error($f['name']) ring-2 ring-red-300 @enderror">
                        @error($f['name'])
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_default" value="1"
                               class="w-4 h-4 rounded accent-brand-600">
                        <span class="text-sm text-ink-700">Set as default address</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary text-sm">Save Address</button>
                <button type="button" @click="showForm = false"
                        class="btn-secondary text-sm">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Address list --}}
    @if($addresses->isEmpty())
        <x-empty-state
            icon="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
            title="No addresses saved"
            message="Add an address to speed up checkout."
            size="md"
        />
    @else
        <div class="space-y-3">
            @foreach($addresses as $address)
                <div class="card p-5 flex items-start justify-between gap-4
                             {{ $address->is_default ? 'border-brand-200 bg-brand-50/30' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 bg-ink-100 rounded-xl flex items-center justify-center
                                    flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-ink-500" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="font-semibold text-ink-900 text-sm">{{ $address->fullName() }}</p>
                                <span class="badge bg-ink-100 text-ink-500 text-[10px] px-2">
                                    {{ $address->label }}
                                </span>
                                @if($address->is_default)
                                    <span class="badge bg-brand-100 text-brand-700 text-[10px] px-2">
                                        Default
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-ink-600">{{ $address->oneLiner() }}</p>
                            <p class="text-xs text-ink-400 mt-0.5">{{ $address->phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <form method="POST" action="{{ route('addresses.destroy', $address) }}"
                              onsubmit="return confirm('Delete this address?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 rounded-lg flex items-center justify-center
                                           text-ink-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection