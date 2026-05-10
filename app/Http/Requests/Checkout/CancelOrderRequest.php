<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CancelOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        // User must own the order
        return $order && $order->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return []; // No input needed — action is on the route model
    }

    public function failedAuthorization()
    {
        abort(403, 'You do not have permission to cancel this order.');
    }
}