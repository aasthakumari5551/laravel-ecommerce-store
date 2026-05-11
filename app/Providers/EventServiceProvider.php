<?php

namespace App\Providers;

use App\Events\LowStockDetected;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Listeners\HandleLowStockDetected;
use App\Listeners\HandleOrderPlaced;
use App\Listeners\HandleOrderStatusChanged;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            HandleOrderPlaced::class,
        ],
        OrderStatusChanged::class => [
            HandleOrderStatusChanged::class,
        ],
        LowStockDetected::class => [
            HandleLowStockDetected::class,
        ],
    ];
}