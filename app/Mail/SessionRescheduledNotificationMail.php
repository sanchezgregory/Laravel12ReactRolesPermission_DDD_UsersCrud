<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessionRescheduledNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $client,
        public $mediator,
        public $newScheduledAt,
        public $oldScheduledAt = null
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cambio en la Fecha de tu Sesión de Mediación',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.session-rescheduled-notification',
            with: [
                'clientName' => $this->client->name,
                'mediatorName' => $this->mediator->name,
                'mediatorEmail' => $this->mediator->email,
                'newScheduledAt' => $this->newScheduledAt,
                'oldScheduledAt' => $this->oldScheduledAt,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
