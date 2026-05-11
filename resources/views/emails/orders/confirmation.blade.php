<x-mail::message>
# Order Confirmed ✓

Hi {{ $order->user->name }},

Your order has been placed and payment received. Here's your summary:

**Order:** {{ $order->number }}
**Date:** {{ $order->created_at->format('d M Y, h:i A') }}

<x-mail::table>
| Product | Qty | Price |
|:--------|:---:|------:|
@foreach ($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | ₹{{ number_format($item->subtotal, 2) }} |
@endforeach
| | **Shipping** | ₹{{ number_format($order->shipping_amount, 2) }} |
| | **Tax** | ₹{{ number_format($order->tax_amount, 2) }} |
| | **Total** | **₹{{ number_format($order->total, 2) }}** |
</x-mail::table>

**Ships to:** {{ $order->shipping_first_name }} {{ $order->shipping_last_name }},
{{ $order->shipping_line1 }}, {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_pincode }}

<x-mail::button :url="$actionUrl" color="primary">
Track Your Order
</x-mail::button>

Thanks for shopping with us!
</x-mail::message>