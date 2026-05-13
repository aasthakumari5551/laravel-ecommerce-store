@extends('layouts.app')
@section('title', 'Compare Products')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('shop.products.index') }}" class="btn-ghost p-2 -ml-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-display text-2xl text-ink-900">Compare Products</h1>
    </div>

    @if($products->isEmpty())
        <x-empty-state
            title="No products to compare"
            message="Add products to compare from the product listing."
            :action="route('shop.products.index')"
            action-label="Browse Products"
        />
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[500px]">

                {{-- Images --}}
                <thead>
                    <tr>
                        <th class="w-40 py-3 text-left text-xs font-semibold text-ink-400
                                    uppercase tracking-wider">
                            Product
                        </th>
                        @foreach($products as $p)
                            <th class="px-4 py-3">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-28 h-28 rounded-xl overflow-hidden
                                                 bg-ink-50 border border-ink-100 mx-auto">
                                        @if($p->primaryImage)
                                            <img src="{{ $p->primaryImage->url }}"
                                                 alt="{{ $p->name }}"
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <a href="{{ route('shop.products.show', $p) }}"
                                       class="text-sm font-semibold text-ink-900
                                              hover:text-brand-700 text-center line-clamp-2">
                                        {{ $p->name }}
                                    </a>
                                    <p class="text-base font-bold text-ink-900">
                                        ₹{{ number_format($p->price, 0) }}
                                    </p>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-ink-100">

                    {{-- Add to cart row --}}
                    <tr class="bg-ink-50/50">
                        <td class="py-4 text-sm font-medium text-ink-600 pr-4"></td>
                        @foreach($products as $p)
                            <td class="px-4 py-4 text-center">
                                @unless($p->isOutOfStock())
                                    <form method="POST" action="{{ route('cart.store') }}"
                                          data-cart-form>
                                        @csrf
                                        <input type="hidden" name="product_id"
                                               value="{{ $p->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                                class="btn-primary text-sm px-4 py-2 w-full
                                                       justify-center">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-ink-100 text-ink-500">
                                        Out of Stock
                                    </span>
                                @endunless
                            </td>
                        @endforeach
                    </tr>

                    {{-- Spec rows --}}
                    @php
                        $rows = [
                            ['label' => 'Brand',    'key' => 'brand'],
                            ['label' => 'Category', 'key' => 'category.name'],
                            ['label' => 'Rating',   'key' => null],
                            ['label' => 'Reviews',  'key' => 'review_count'],
                            ['label' => 'Price',    'key' => null],
                            ['label' => 'Stock',    'key' => null],
                            ['label' => 'SKU',      'key' => 'sku'],
                        ];
                    @endphp

                    @foreach($rows as $row)
                        <tr class="{{ $loop->even ? 'bg-ink-50/30' : '' }}">
                            <td class="py-3.5 text-sm font-semibold text-ink-600 pr-4">
                                {{ $row['label'] }}
                            </td>
                            @foreach($products as $p)
                                <td class="px-4 py-3.5 text-sm text-ink-800 text-center">
                                    @if($row['label'] === 'Rating')
                                        <div class="flex items-center justify-center gap-1">
                                            <span class="text-brand-500 font-bold">
                                                {{ number_format($p->avg_rating, 1) }}
                                            </span>
                                            <svg class="w-3.5 h-3.5 text-brand-500"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                    @elseif($row['label'] === 'Price')
                                        <div>
                                            <p class="font-bold">
                                                ₹{{ number_format($p->price, 0) }}
                                            </p>
                                            @if($p->isOnSale())
                                                <p class="text-xs text-green-600 font-medium">
                                                    Save {{ $p->discountPercentage() }}%
                                                </p>
                                            @endif
                                        </div>
                                    @elseif($row['label'] === 'Stock')
                                        @if($p->isOutOfStock())
                                            <span class="badge bg-red-100 text-red-600">
                                                Out of Stock
                                            </span>
                                        @elseif($p->isLowStock())
                                            <span class="badge bg-orange-100 text-orange-600">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="badge bg-green-100 text-green-700">
                                                In Stock
                                            </span>
                                        @endif
                                    @else
                                        {{ data_get($p, $row['key']) ?? '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection