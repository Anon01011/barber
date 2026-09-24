<?php

namespace App\Mail;

use App\Models\Salon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $salon;
    public $stats;
    public $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Salon $salon, array $stats, $date)
    {
        $this->salon = $salon;
        $this->stats = $stats;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dateFormatted = $this->date->format('F j, Y');

        return $this->subject("Daily Summary: {$this->salon->name} - {$dateFormatted}")
            ->view('emails.daily_summary');
    }
}
