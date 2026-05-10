<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment — Demo Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Sora:wght@300;400;600;700&display=swap');
        * { font-family: 'Sora', sans-serif; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .gateway-card {
            background: linear-gradient(135deg, #0f0f11 0%, #1a1a2e 50%, #0f0f11 100%);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 0 60px rgba(99, 102, 241, 0.08), 0 25px 60px rgba(0,0,0,0.5);
        }
        .success-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.35);
            transition: all 0.2s;
        }
        .success-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(16,185,129,0.45); }
        .failure-btn {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.4);
            transition: all 0.2s;
        }
        .failure-btn:hover { background: rgba(239, 68, 68, 0.18); }
        .badge { background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); }
        .divider { border-color: rgba(255,255,255,0.07); }
        .item-row { border-bottom: 1px solid rgba(255,255,255,0.05); }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(99,102,241,0.4); }
            70%  { box-shadow: 0 0 0 10px rgba(99,102,241,0); }
            100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
        }
        .lock-icon { animation: pulse-ring 2.5s cubic-bezier(0.455,0.03,0.515,0.955) infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4"
      style="background: radial-gradient(ellipse at 50% 0%, #1a1a3e 0%, #080810 60%);">

    <div class="w-full max-w-md">

        {{-- Gateway header --}}
        <div class="text-center mb-6">
            <div class="inline-flex items-center gap-2 badge rounded-full px-4 py-1.5 mb-4">
                <svg class="w-3.5 h-3.5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-indigo-300 text-xs tracking-widest uppercase font-semibold">Demo Payment Gateway</span>
            </div>
            <h1 class="text-white text-2xl font-bold tracking-tight">Complete Your Payment</h1>
            <p class="text-gray-500 text-sm mt-1 mono">Order {{ $order->number }}</p>
        </div>

        {{-- Main card --}}
        <div class="gateway-card rounded-2xl p-6">

            {{-- Lock indicator --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-full bg-indigo-600 lock-icon flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-sm font-semibold">256-bit SSL secured</p>
                    <p class="text-gray-500 text-xs">Your payment data is protected</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-gray-400 text-xs">Total to pay</p>
                    <p class="text-white text-xl font-bold mono">₹{{ number_format($amount, 2) }}</p>
                </div>
            </div>

            <hr class="divider mb-5">

            {{-- Order summary --}}
            <p class="text-gray-400 text-xs uppercase tracking-wider mb-3 font-semibold">Order Summary</p>
            <div class="space-y-0 mb-5">
                @foreach ($order->items as $item)
                    <div class="item-row flex justify-between items-center py-2.5">
                        <div>
                            <p class="text-gray-200 text-sm">{{ $item->product_name }}</p>
                            <p class="text-gray-500 text-xs mono">× {{ $item->quantity }}</p>
                        </div>
                        <p class="text-gray-300 text-sm mono">₹{{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Totals --}}
            <div class="bg-white/[0.03] rounded-xl p-4 mb-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Subtotal</span>
                    <span class="text-gray-300 mono">₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">Shipping</span>
                    <span class="text-gray-300 mono">
                        @if ($order->shipping_amount == 0)
                            <span class="text-emerald-400">Free</span>
                        @else
                            ₹{{ number_format($order->shipping_amount, 2) }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">GST (18%)</span>
                    <span class="text-gray-300 mono">₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                <hr class="divider">
                <div class="flex justify-between font-bold">
                    <span class="text-white">Total</span>
                    <span class="text-white mono text-lg">₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <form method="POST" action="{{ route('checkout.demo.success') }}" class="mb-3">
                @csrf
                <button type="submit"
                        class="success-btn w-full text-white font-semibold py-3.5 rounded-xl text-sm tracking-wide">
                    ✓ Pay ₹{{ number_format($amount, 2) }} Now
                </button>
            </form>

            <form method="POST" action="{{ route('checkout.demo.failure') }}">
                @csrf
                <button type="submit"
                        class="failure-btn w-full text-red-400 font-medium py-2.5 rounded-xl text-sm">
                    ✕ Cancel Payment
                </button>
            </form>

            {{-- Demo notice --}}
            <p class="text-center text-gray-600 text-xs mt-5 mono leading-relaxed">
                ⚡ DEMO MODE — No real transaction occurs.<br>
                Gateway ID: <span class="text-gray-500">{{ $gatewayOrderId }}</span>
            </p>
        </div>
    </div>
</body>
</html>