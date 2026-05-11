<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database']; // stored in notifications table for in-app bell icon
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'order_placed',
            'order_id'     => $this->order->id,
            'order_number' => $this->order->number,
            'total'        => $this->order->total,
            'message'      => "Your order {$this->order->number} has been confirmed.",
            'url'          => route('orders.show', $this->order),
        ];
    }
}