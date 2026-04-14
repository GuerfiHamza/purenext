<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $itemName,
        public float  $currentStock,
        public float  $minStock,
        public string $unit,
        public string $type = 'raw_material' // raw_material ou finished_good
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Alerte Stock Bas — ' . $this->itemName,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.low-stock',
        );
    }
}