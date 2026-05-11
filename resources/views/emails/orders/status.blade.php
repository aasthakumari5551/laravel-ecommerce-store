<x-mail::message>
# Order Update: {{ $newStatus->label() }}

Hi {{ $order->user->name }},

@switch($newStatus->value)
    @case('shipped')
        Great news — your order **{{ $order->number }}** has been shipped and is on its way!
        @break
    @case('delivered')
        Your order **{{ $order->number }}** has been delivered. We hope you love your purchase!
        @break
    @case('cancelled')
        Your order **{{ $order->number }}** has been cancelled.
        If you paid, a refund will be processed shortly.
        @break
    @case('refunded')
        Your refund for order **{{ $order->number }}** has been initiated.
        Please allow 5–7 business days to reflect in your account.
        @break
    @default
        Your order **{{ $order->number }}** status has been updated to **{{ $newStatus->label() }}**.
@endswitch

<x-mail::button :url="$actionUrl">
View Order Details
</x-mail::button>

Thanks,
The Team
</x-mail::message>