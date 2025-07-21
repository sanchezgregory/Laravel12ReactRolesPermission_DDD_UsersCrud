<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class EventServiceProviders extends ServiceProvider
{
    protected $listen = [
        \App\Listeners\UserCacheSubscriber::class,
    ];
}
