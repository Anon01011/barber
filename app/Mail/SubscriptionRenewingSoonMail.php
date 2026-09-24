<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewingSoonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $daysUntilRenewal;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, int $daysUntilRenewal)
    {
        $this->subscription = $subscription;
        $this->daysUntilRenewal = $daysUntilRenewal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->daysUntilRenewal === 1
            ? 'Your Subscription Renews Tomorrow'
            : "Your Subscription Renews in {$this->daysUntilRenewal} Days";

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-renewing-soon',
            with: [
                'salon' => $this->subscription->salon,
                'subscription' => $this->subscription,
                'plan' => $this->subscription->plan,
                'daysUntilRenewal' => $this->daysUntilRenewal,
                'renewalDate' => $this->subscription->ends_at,
                'amount' => $this->subscription->plan->price,
            ],
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
