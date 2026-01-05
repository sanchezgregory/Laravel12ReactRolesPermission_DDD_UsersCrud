<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class StripeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        Stripe::setApiVersion(config('services.stripe.api_version'));
        Stripe::setMaxNetworkRetries(2);
    }
}
