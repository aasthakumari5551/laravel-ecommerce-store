<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->uuid,
            'number'         => $this->number,
            'status'         => $this->status->value,
            'status_label'   => $this->status->label(),
            'payment_status' => $this->payment_status->value,
            'subtotal'       => (float) $this->subtotal,
            'discount'       => (float) $this->discount_amount,
            'shipping'       => (float) $this->shipping_amount,
            'tax'            => (float) $this->tax_amount,
            'total'          => (float) $this->total,
            'paid_at'        => $this->paid_at?->toIso8601String(),
            'created_at'     => $this->created_at->toIso8601String(),
            'items'          => $this->when(
                $this->relationLoaded('items'),
                fn () => $this->items->map(fn ($item) => [
                    'product_name' => $item->product_name,
                    'quantity'     => $item->quantity,
                    'unit_price'   => (float) $item->unit_price,
                    'subtotal'     => (float) $item->subtotal,
                ])
            ),
        ];
    }
}