@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-400 hover:underline">
                ← Orders
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $order->number }}</h1>
            <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="text-right space-y-1">
            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                bg-{{ $order->status->color() }}-100 text-{{ $order->status->color() }}-700">
                {{ $order->status->label() }}
            </span>
            <p class="text-xs text-gray-400">Payment: {{ $order->payment_status->label() }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-6 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->has('status'))
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-6 text-sm">
            {{ $errors->first('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: items + address + timeline --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Items --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-900">Items</div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between items-center px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->product_name }}</p>
                                @if ($item->product_sku)
                                    <p class="text-xs font-mono text-gray-400">{{ $item->product_sku }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-0.5">× {{ $item->quantity }} @ ₹{{ number_format($item->unit_price, 2) }}</p>
                            </div>
                            <p class="font-semibold text-sm text-gray-900">₹{{ number_format($item->subtotal, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl space-y-1.5 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600"><span>Shipping</span><span>{{ $order->shipping_amount == 0 ? 'Free' : '₹'.number_format($order->shipping_amount, 2) }}</span></div>
                    <div class="flex justify-between text-gray-600"><span>Tax</span><span>₹{{ number_format($order->tax_amount, 2) }}</span></div>
                    <div class="flex justify-between font-bold text-gray-900 text-base pt-1 border-t border-gray-200">
                        <span>Total</span><span>₹{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <h2 class="font-semibold text-gray-900 mb-4">Status Timeline</h2>
                <div class="space-y-4">
                    @foreach ($order->statusHistories as $history)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 mt-0.5 flex-shrink-0"></div>
                                @if (! $loop->last)
                                    <div class="w-px flex-1 bg-gray-200 my-1"></div>
                                @endif
                            </div>
                            <div class="pb-3">
                                <p class="text-sm font-medium text-gray-900">{{ $history->status->label() }}</p>
                                @if ($history->comment)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $history->comment }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $history->created_at->format('d M Y, h:i A') }}
                                    @if ($history->changedBy)
                                        · {{ $history->changedBy->name }}
                                    @else
                                        · System
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: customer + address + status action --}}
        <div class="space-y-6">

            {{-- Customer --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <h2 class="font-semibold text-gray-900 mb-3">Customer</h2>
                <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
            </div>

            {{-- Shipping address --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <h2 class="font-semibold text-gray-900 mb-3">Ship To</h2>
                <div class="text-sm text-gray-600 space-y-0.5">
                    <p class="font-medium text-gray-900">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                    <p>{{ $order->shipping_line1 }}</p>
                    @if ($order->shipping_line2) <p>{{ $order->shipping_line2 }}</p> @endif
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}</p>
                    <p>{{ $order->shipping_country }}</p>
                    <p class="mt-1">{{ $order->shipping_phone }}</p>
                </div>
            </div>

            {{-- Payment info --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                <h2 class="font-semibold text-gray-900 mb-3">Payment</h2>
                <div class="text-sm space-y-1">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Method</span>
                        <span class="text-gray-900 font-mono text-xs">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="font-semibold {{ $order->isPaid() ? 'text-green-600' : 'text-gray-400' }}">
                            {{ $order->payment_status->label() }}
                        </span>
                    </div>
                    @if ($order->razorpay_payment_id)
                        <div class="pt-1">
                            <p class="text-gray-400 text-xs">Payment ID</p>
                            <p class="font-mono text-xs text-gray-600 break-all">{{ $order->razorpay_payment_id }}</p>
                        </div>
                    @endif
                    @if ($order->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Paid at</span>
                            <span class="text-gray-600 text-xs">{{ $order->paid_at->format('d M Y, h:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status update --}}
            @if (count($allowedTransitions) > 0)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-6 py-5">
                    <h2 class="font-semibold text-gray-900 mb-4">Update Status</h2>
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-3">
                            <select name="status" required
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <option value="">Select new status…</option>
                                @foreach ($allowedTransitions as $transition)
                                    <option value="{{ $transition->value }}">{{ $transition->label() }}</option>
                                @endforeach
                            </select>
                            <textarea name="comment" rows="2"
                                      placeholder="Optional note…"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 resize-none"></textarea>
                            <button type="submit"
                                    class="w-full bg-indigo-600 text-white font-semibold py-2.5 rounded-lg text-sm hover:bg-indigo-700 transition">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection