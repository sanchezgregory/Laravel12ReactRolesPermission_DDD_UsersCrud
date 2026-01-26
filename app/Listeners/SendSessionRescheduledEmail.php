<?php

namespace App\Listeners;

use App\Events\SessionRescheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSessionRescheduledEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SessionRescheduled $event): void
    {
        if ($event->client && $event->client->email) {
            \Illuminate\Support\Facades\Mail::to($event->client->email)->send(
                new \App\Mail\SessionRescheduledNotificationMail(
                    $event->client,
                    $event->mediator,
                    $event->scheduledAt,
                    $event->oldScheduledAt
                )
            );
        }
    }
}
