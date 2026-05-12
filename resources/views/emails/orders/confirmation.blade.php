<x-velura-layout>
    <h1>Order Confirmed ✓</h1>
    <p>Hi <strong>{{ $order->user->name }}</strong>,</p>
    <p>
        We've received your order and payment has been confirmed.
        Here's your summary:
    </p>

    <div class="divider"></div>

    <p style="margin-bottom:4px;">
        <strong>Order Number:</strong> {{ $order->number }}<br>
        <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}
    </p>

    <table class="order">
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td style="text-align:right">{{ $item->quantity }}</td>
                    <td style="text-align:right">₹{{ number_format($item->subtotal, 0) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align:right;color:#6b5c48">Shipping</td>
                <td style="text-align:right">
                    {{ $order->shipping_amount == 0 ? 'Free' : '₹'.number_format($order->shipping_amount,0) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;color:#6b5c48">GST (18%)</td>
                <td style="text-align:right">₹{{ number_format($order->tax_amount, 0) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align:right">Total</td>
                <td style="text-align:right">₹{{ number_format($order->total, 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <p>
        <strong>Delivering to:</strong><br>
        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }},
        {{ $order->shipping_line1 }}, {{ $order->shipping_city }},
        {{ $order->shipping_state }} {{ $order->shipping_pincode }}
    </p>

    <a href="{{ $actionUrl }}" class="btn">Track Your Order →</a>

    <p style="color:#a8987f;font-size:12px;">
        If you have any questions, reply to this email or contact us at
        <a href="mailto:{{ config('brand.support') }}" style="color:#d97706;">
            {{ config('brand.support') }}
        </a>
    </p>
</x-velura-layout>