<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Mail\Traits\UsesEmailTemplates;

class BookingCompletedMail extends Mailable
{
    use Queueable, SerializesModels, UsesEmailTemplates;

    public $booking;
    public $ratingUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->ratingUrl = $booking->getRatingUrl();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Thank You! Rate Your Experience - ' . ($this->booking->salon->name ?? 'Salon CMS');

        return new Envelope(
            subject: $this->getEmailSubject('booking_completed', $defaultSubject),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return $this->getEmailContent('booking_completed', 'emails.bookings.completed');
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
