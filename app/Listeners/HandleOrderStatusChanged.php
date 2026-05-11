<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendOrderStatusEmail;

class HandleOrderStatusChanged
{
    public function handle(OrderStatusChanged $event): void
    {
        SendOrderStatusEmail::dispatch($event->order, $event->newStatus);
    }
}