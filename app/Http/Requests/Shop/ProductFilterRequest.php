<?php

namespace App\Http\Requests\Shop;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'q'          => ['nullable', 'string', 'max:100'],
            'category'   => ['nullable', 'string', 'max:100'],
            'min_price'  => ['nullable', 'numeric', 'min:0'],
            'max_price'  => ['nullable', 'numeric', 'min:0'],
            'min_rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'in_stock'   => ['nullable', 'boolean'],
            'featured'   => ['nullable', 'boolean'],
            'sort'       => ['nullable', 'in:price_asc,price_desc,newest,rating,popular'],
            'page'       => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function filters(): array
    {
        return $this->only([
            'q', 'category', 'min_price', 'max_price',
            'min_rating', 'in_stock', 'featured', 'sort',
        ]);
    }
}