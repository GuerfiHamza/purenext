<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductionCompleteAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $batchNumber,
        public int    $packetsActual,
        public int    $packetsEstimated,
        public string $recipeName,
        public string $operatorName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Production Terminée — ' . $this->batchNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.production-complete',
        );
    }
}