<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionWelcome extends Mailable
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
                'subscription_welcome',
                'Welcome to ' . config('app.name') . '!',
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
            'subscription_welcome',
            'emails.subscription.welcome',
            $this->getVariables()
        );
    }

    protected function getVariables(): array
    {
        return [
            'app_name' => config('app.name'),
            'salon_name' => $this->salon->name,
            'plan_name' => $this->plan->name,
            'trial_days' => $this->plan->trial_days,
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
