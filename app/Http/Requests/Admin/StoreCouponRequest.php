<?php

namespace App\Http\Requests\Admin;

use App\Enums\DiscountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCouponRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'code'                   => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'description'            => ['nullable', 'string', 'max:255'],
            'discount_type'          => ['required', new Enum(DiscountType::class)],
            'discount_value'         => ['required', 'numeric', 'min:0'],
            'minimum_order_amount'   => ['nullable', 'numeric', 'min:0'],
            'maximum_discount'       => ['nullable', 'numeric', 'min:0'],
            'usage_limit'            => ['nullable', 'integer', 'min:1'],
            'usage_limit_per_user'   => ['nullable', 'integer', 'min:1'],
            'is_active'              => ['boolean'],
            'starts_at'              => ['nullable', 'date'],
            'expires_at'             => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}