<?php

namespace App\Providers;

use App\Contracts\PaymentGateway;
use App\Models\Address;
use App\Models\Category;
use App\Models\Review;
use App\Observers\CategoryObserver;
use App\Policies\AddressPolicy;
use App\Policies\ReviewPolicy;
use App\Services\SimulatedPaymentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, SimulatedPaymentService::class);
    }

    public function boot(): void
{
    // Policies
    Gate::policy(Address::class, AddressPolicy::class);
    Gate::policy(Review::class,  ReviewPolicy::class);

    // Observers
    Category::observe(CategoryObserver::class);

    // Pagination
    Paginator::defaultView('components.pagination');
    Paginator::defaultSimpleView('components.pagination');

    // Shared navbar/footer categories
    \Illuminate\Support\Facades\View::composer(
        ['layouts.partials.navbar', 'layouts.partials.footer'],
        function (\Illuminate\View\View $view) {

            $view->with(
                'navCategories',
                app(\App\Services\CacheService::class)
                    ->categories()
            );
        }
    );
}
}