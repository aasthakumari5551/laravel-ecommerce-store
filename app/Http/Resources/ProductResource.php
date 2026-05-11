<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->uuid,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'short_description' => $this->short_description,
            'price'             => (float) $this->price,
            'compare_price'     => $this->compare_price ? (float) $this->compare_price : null,
            'on_sale'           => $this->isOnSale(),
            'discount_pct'      => $this->discountPercentage(),
            'stock'             => $this->track_inventory ? $this->stock : null,
            'in_stock'          => ! $this->isOutOfStock(),
            'is_featured'       => $this->is_featured,
            'avg_rating'        => (float) $this->avg_rating,
            'review_count'      => $this->review_count,
            'category'          => new CategoryResource($this->whenLoaded('category')),
            'primary_image_url' => $this->primaryImage?->url,
            'images'            => $this->when(
                $this->relationLoaded('images'),
                fn () => $this->images->map(fn ($img) => [
                    'url'        => $img->url,
                    'alt'        => $img->alt_text,
                    'is_primary' => $img->is_primary,
                ])
            ),
        ];
    }
}