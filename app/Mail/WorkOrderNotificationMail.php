<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Email template for work-order notifications sent to clients.
 */
class WorkOrderNotificationMail extends Mailable
{
    public function __construct(
        public string $subjectLine,
        public string $headline,
        public string $body,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.work-order-notification');
    }
}