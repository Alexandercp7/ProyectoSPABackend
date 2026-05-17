<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Email used to share a work order portal link and QR code with a client.
 */
class WorkOrderShareMail extends Mailable
{
    public function __construct(
        public string $orderId,
        public string $portalUrl,
        public string $qrUrl,
        public string $clientName,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Seguimiento de tu orden {$this->orderId}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.work-order-share');
    }
}