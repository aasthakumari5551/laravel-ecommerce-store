<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderConfirmationEmail;

class HandleOrderPlaced
{
    public function handle(OrderPlaced $event): void
    {
        SendOrderConfirmationEmail::dispatch($event->order)
            ->delay(now()->addSeconds(5)); // small delay — let DB commit settle
    }
}