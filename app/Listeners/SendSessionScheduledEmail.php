<?php

namespace App\Listeners;

use App\Events\SessionScheduled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSessionScheduledEmail implements ShouldQueue
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
    public function handle(SessionScheduled $event): void
    {
        if ($event->mediator && $event->mediator->email) {
            \Illuminate\Support\Facades\Mail::to($event->mediator->email)->send(
                new \App\Mail\SessionScheduledConfirmationMail(
                    $event->mediator,
                    $event->user,
                    $event->scheduledAt,
                    $event->notes
                )
            );
        }
    }
}
