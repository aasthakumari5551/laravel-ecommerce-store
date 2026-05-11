<?php

namespace App\Mail;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Order       $order,
        public readonly OrderStatus $newStatus,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match($this->newStatus) {
            OrderStatus::Shipped   => "Your Order {$this->order->number} Has Shipped 🚚",
            OrderStatus::Delivered => "Your Order {$this->order->number} Is Delivered ✅",
            OrderStatus::Cancelled => "Order {$this->order->number} Cancelled",
            OrderStatus::Refunded  => "Refund Initiated for {$this->order->number}",
            default                => "Update on Your Order {$this->order->number}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.status',
            with: [
                'order'      => $this->order,
                'newStatus'  => $this->newStatus,
                'actionUrl'  => route('orders.show', $this->order),
            ],
        );
    }
}