<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiredMail extends Mailable
{
    use Queueable, SerializesModels, \App\Mail\Traits\UsesSystemEmailTemplates;

    public $subscription;
    public $salon;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
        $this->salon = $subscription->salon;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getEmailSubject(
                'subscription_expired',
                'Your Subscription Has Expired',
                $this->getVariables()
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return $this->getEmailContent(
            'subscription_expired',
            'emails.subscription.expired',
            $this->getVariables()
        );
    }

    protected function getVariables(): array
    {
        return [
            'app_name' => config('app.name'),
            'salon_name' => $this->salon->name,
            'plan_name' => $this->subscription->plan->name,
            'expiry_date' => $this->subscription->ends_at->format('M d, Y'),
            'renew_url' => url('/'),
        ];
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
