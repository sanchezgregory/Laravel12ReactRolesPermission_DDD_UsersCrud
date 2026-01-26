<?php

namespace App\Providers;

use App\Events\SessionConfirmedByMediator;
use App\Events\SessionRescheduled;
use App\Events\SessionScheduled;
use App\Listeners\SendSessionConfirmedEmail;
use App\Listeners\SendSessionRescheduledEmail;
use App\Listeners\SendSessionScheduledEmail;
use App\Listeners\UserCacheSubscriber;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SessionConfirmedByMediator::class => [
            SendSessionConfirmedEmail::class,
        ],
        SessionRescheduled::class => [
            SendSessionRescheduledEmail::class,
        ],
        SessionScheduled::class => [
            SendSessionScheduledEmail::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        UserCacheSubscriber::class,
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
