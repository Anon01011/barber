<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringSoonMail extends Mailable
{
    use Queueable, SerializesModels, \App\Mail\Traits\UsesSystemEmailTemplates;

    public $subscription;
    public $salon;
    public $daysLeft;

    /**
     * Create a new message instance.
     */
    public function __construct(Subscription $subscription, int $daysLeft)
    {
        $this->subscription = $subscription;
        $this->salon = $subscription->salon;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getEmailSubject(
                'subscription_expiring_soon',
                'Your Subscription Expires Soon',
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
            'subscription_expiring_soon',
            'emails.subscription.expiring_soon',
            $this->getVariables()
        );
    }

    protected function getVariables(): array
    {
        return [
            'app_name' => config('app.name'),
            'salon_name' => $this->salon->name,
            'plan_name' => $this->subscription->plan->name,
            'days_left' => $this->daysLeft,
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
