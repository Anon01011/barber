<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewed extends Mailable
{
    use Queueable, SerializesModels, \App\Mail\Traits\UsesSystemEmailTemplates;

    public $salon;
    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct($salon, $subscription)
    {
        $this->salon = $salon;
        $this->subscription = $subscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getEmailSubject(
                'subscription_renewed',
                'Subscription Renewed Successfully',
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
            'subscription_renewed',
            'emails.subscription.renewed',
            $this->getVariables()
        );
    }

    protected function getVariables(): array
    {
        return [
            'app_name' => config('app.name'),
            'salon_name' => $this->salon->name,
            'plan_name' => $this->subscription->plan->name,
            'end_date' => $this->subscription->ends_at->format('M d, Y'),
            'dashboard_url' => route('dashboard', ['salon_slug' => $this->salon->slug]),
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
