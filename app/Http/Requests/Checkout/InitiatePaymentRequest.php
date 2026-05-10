<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Validate that the address belongs to the authenticated user
            'address_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('addresses', 'id')
                    ->where('user_id', $this->user()->id),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.exists' => 'Please select a valid delivery address.',
        ];
    }
}