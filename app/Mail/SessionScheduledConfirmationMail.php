<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SessionScheduledConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public $mediator,
        public $user,
        public $scheduledAt,
        public $notes = null
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva Sesión Agendada - Confirmación Pendiente',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.session-scheduled-confirmation',
            with: [
                'mediatorName' => $this->mediator->name,
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'scheduledAt' => $this->scheduledAt,
                'notes' => $this->notes,
                'calendlyUrl' => $this->mediator->calendly_url ?? null,
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
