<?php

namespace App\Jobs;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60; // seconds between retries

    public function __construct(public readonly Order $order) {}

    public function handle(): void
    {
        $this->order->load(['user', 'items']);

        Mail::to($this->order->user->email)
            ->send(new OrderConfirmationMail($this->order));

        // In-app notification
        $this->order->user->notify(
            new \App\Notifications\OrderPlacedNotification($this->order)
        );
    }
}