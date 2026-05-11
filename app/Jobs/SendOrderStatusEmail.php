<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60;

    // Only email for these meaningful status changes
    private const NOTIFY_FOR = [
        OrderStatus::Confirmed,
        OrderStatus::Shipped,
        OrderStatus::Delivered,
        OrderStatus::Cancelled,
        OrderStatus::Refunded,
    ];

    public function __construct(
        public readonly Order       $order,
        public readonly OrderStatus $newStatus,
    ) {}

    public function handle(): void
    {
        if (! in_array($this->newStatus, self::NOTIFY_FOR, strict: true)) {
            return;
        }

        $this->order->load('user');

        Mail::to($this->order->user->email)
            ->send(new OrderStatusMail($this->order, $this->newStatus));
    }
}