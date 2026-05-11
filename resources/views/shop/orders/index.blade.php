@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    <h1 class="font-display text-3xl text-ink-900 mb-7">My Orders</h1>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3
                    text-sm mb-5">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->isEmpty())
        <div class="text-center py-24 card">
            <div class="w-20 h-20 bg-ink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-9 h-9 text-ink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="font-semibold text-ink-700 text-lg mb-1">No orders yet</p>
            <p class="text-ink-400 text-sm mb-6">Your order history will appear here.</p>
            <a href="{{ route('shop.products.index') }}" class="btn-primary">Start Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order) }}"
                   class="card p-5 flex flex-col sm:flex-row sm:items-center gap-4
                          hover:border-brand-200 hover:shadow-card-hover transition-all group block">

                    {{-- Status indicator --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                bg-{{ $order->status->color() }}-100">
                        <svg class="w-5 h-5 text-{{ $order->status->color() }}-600"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>

                    {{-- Order info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <p class="font-semibold text-ink-900 font-mono text-sm">
                                {{ $order->number }}
                            </p>
                            <span class="badge bg-{{ $order->status->color() }}-100
                                         text-{{ $order->status->color() }}-700 text-xs">
                                {{ $order->status->label() }}
                            </span>
                            @if($order->isPaid())
                                <span class="badge bg-green-100 text-green-700 text-xs">
                                    Paid
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-ink-500">
                            {{ $order->created_at->format('d M Y') }}
                            · {{ $order->items_count }} item{{ $order->items_count !== 1 ? 's' : '' }}
                        </p>
                    </div>

                    {{-- Total + arrow --}}
                    <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                        <p class="font-bold text-ink-900">
                            ₹{{ number_format($order->total, 0) }}
                        </p>
                        <svg class="w-4 h-4 text-ink-300 group-hover:text-brand-500
                                    group-hover:translate-x-0.5 transition-all"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">{{ $orders->links('components.pagination') }}</div>
    @endif
</div>
@endsection