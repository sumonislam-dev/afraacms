<?php

namespace App\Mail;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Donation $donation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your donation receipt - :number', ['number' => $this->donation->receipt_number]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.donation-receipt',
        );
    }
}
