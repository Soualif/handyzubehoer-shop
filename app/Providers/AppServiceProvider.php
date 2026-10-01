<?php

namespace App\Providers;

use App\Models\Category;
use App\Payments\PaymentGateway;
use App\Payments\StripeGateway;
use App\Suppliers\CjDropshipping;
use App\Suppliers\Supplier;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, fn () => new StripeGateway(
            new StripeClient(['api_key' => config('services.stripe.secret') ?: null]),
        ));

        $this->app->bind(Supplier::class, CjDropshipping::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.shop', function ($view) {
            $view->with('categories', Category::orderBy('position')->get());
        });
    }
}
