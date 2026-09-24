<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionApproved extends Mailable
{
    use Queueable, SerializesModels, \App\Mail\Traits\UsesSystemEmailTemplates;

    public $salon;
    public $plan;

    /**
     * Create a new message instance.
     */
    public function __construct($salon, $plan)
    {
        $this->salon = $salon;
        $this->plan = $plan;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getEmailSubject(
                'subscription_approved',
                'Your Salon Has Been Approved! - ' . config('app.name'),
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
            'subscription_approved',
            'emails.subscription.approved',
            $this->getVariables()
        );
    }

    protected function getVariables(): array
    {
        return [
            'app_name' => config('app.name'),
            'salon_name' => $this->salon->name,
            'plan_name' => $this->plan->name,
            'dashboard_url' => route('dashboard', ['salon_slug' => $this->salon->slug]),
            'login_url' => route('login'),
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
