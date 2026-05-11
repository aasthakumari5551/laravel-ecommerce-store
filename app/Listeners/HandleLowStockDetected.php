<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use App\Jobs\NotifyAdminsLowStock;

class HandleLowStockDetected
{
    public function handle(LowStockDetected $event): void
    {
        NotifyAdminsLowStock::dispatch($event->product);
    }
}