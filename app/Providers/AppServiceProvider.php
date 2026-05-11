<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;

use App\Models\Address;
use App\Models\Review;

use App\Policies\AddressPolicy;
use App\Policies\ReviewPolicy;

use App\Services\SimulatedPaymentService;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ── Payment gateway binding ───────────────────────────
        // DEMO: SimulatedPaymentService
        // LIVE: swap to RazorpayService — nothing else changes

        $this->app->bind(
            PaymentGateway::class,
            SimulatedPaymentService::class
        );
    }

    public function boot(): void
    {
        // ── Policies ─────────────────────────────────────────

        Gate::policy(Address::class, AddressPolicy::class);

        Gate::policy(Review::class, ReviewPolicy::class);

        \App\Models\Category::observe(
            \App\Observers\CategoryObserver::class
        );
    }
}