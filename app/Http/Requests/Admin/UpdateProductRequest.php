<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id; // route model binding via uuid

        return [
            'category_id'         => ['required', 'integer', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:280', Rule::unique('products', 'slug')->ignore($productId)],
            'sku'                 => ['nullable', 'string', 'max:100',  Rule::unique('products', 'sku')->ignore($productId)],
            'short_description'   => ['nullable', 'string', 'max:500'],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'compare_price'       => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'cost_price'          => ['nullable', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'track_inventory'     => ['boolean'],
            'is_active'           => ['boolean'],
            'is_featured'         => ['boolean'],
            'meta_title'          => ['nullable', 'string', 'max:255'],
            'meta_description'    => ['nullable', 'string', 'max:500'],
            'images'              => ['nullable', 'array', 'max:10'],
            'images.*'            => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'delete_image_ids'    => ['nullable', 'array'],
            'delete_image_ids.*'  => ['integer'],
        ];
    }
}