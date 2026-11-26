<?php

namespace App\Mail;

use App\Models\Salon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalonBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public Salon $salon;
    public string $filePath;
    public string $fileName;

    public function __construct(Salon $salon, string $filePath, string $fileName)
    {
        $this->salon = $salon;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Automated Data Backup - ' . $this->salon->name . ' (' . now()->format('M d, Y') . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.salon-backup',
            with: [
                'salon' => $this->salon,
                'fileName' => $this->fileName,
                'backupDate' => now()->format('F j, Y g:i A'),
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as($this->fileName)
                ->withMime('application/json'),
        ];
    }
}
