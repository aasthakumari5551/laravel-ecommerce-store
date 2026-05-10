@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold text-gray-900 mb-6">My Orders</h1>

    @if ($orders->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-16 text-center">
            <p class="text-gray-400 text-sm">You haven't placed any orders yet.</p>
            <a href="{{ url('/') }}" class="inline-block mt-4 text-sm text-indigo-600 hover:underline">
                Start shopping →
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}"
                   class="block bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5 hover:border-indigo-200 hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $order->number }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $order->created_at->format('d M Y') }}</p>
                            <p class="text-sm text-gray-600 mt-2">
                                {{ $order->items->count() }} item(s) ·
                                <span class="font-medium">₹{{ number_format($order->total, 2) }}</span>
                            </p>
                        </div>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold
                            bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                            {{ $order->status->label() }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection