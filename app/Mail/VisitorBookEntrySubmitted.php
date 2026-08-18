<?php

namespace App\Mail;

use App\Models\VisitorBookEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitorBookEntrySubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly VisitorBookEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New visitor book entry awaiting approval'),
            replyTo: $this->entry->visitor_email ? [$this->entry->visitor_email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.visitor-book-entry-submitted',
        );
    }
}
