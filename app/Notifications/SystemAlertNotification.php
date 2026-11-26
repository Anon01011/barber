<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $title;
    public string $message;
    public string $level; // 'danger' | 'warning' | 'info' | 'success'

    public function __construct(string $title, string $message, string $level = 'warning')
    {
        $this->title   = $title;
        $this->message = $message;
        $this->level   = $level;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('[Barber SaaS] ' . $this->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('Platform: ' . config('app.url'))
            ->line('Time: ' . now()->format('Y-m-d H:i:s T'));

        if ($this->level === 'danger') {
            $mail->error();
        }

        return $mail->action('Open System Health', route('super-admin.system-health.index'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->level === 'danger' ? 'danger' : ($this->level === 'warning' ? 'warning' : 'info'),
            'link'    => route('super-admin.system-health.index'),
        ];
    }
}
