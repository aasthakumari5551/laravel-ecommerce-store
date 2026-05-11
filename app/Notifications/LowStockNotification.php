<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Product $product) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'low_stock',
            'product_id' => $this->product->id,
            'name'       => $this->product->name,
            'sku'        => $this->product->sku,
            'stock'      => $this->product->stock,
            'message'    => "Low stock: \"{$this->product->name}\" has only {$this->product->stock} units left.",
            'url'        => route('admin.products.edit', $this->product),
        ];
    }
}