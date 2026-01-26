<?php

namespace App\Listeners;

use App\Events\SessionConfirmedByMediator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSessionConfirmedEmail implements ShouldQueue
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
    public function handle(SessionConfirmedByMediator $event): void
    {
        if ($event->client && $event->client->email) {
            \Illuminate\Support\Facades\Mail::to($event->client->email)->send(
                new \App\Mail\SessionConfirmedByMediatorMail(
                    $event->client,
                    $event->mediator,
                    $event->scheduledAt,
                    $event->meetingLink
                )
            );
        }
    }
}
