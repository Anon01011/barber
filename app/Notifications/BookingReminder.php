<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Upcoming Appointment Reminder')
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a friendly reminder about your upcoming appointment.')
            ->line('Service: ' . $this->booking->service->name)
            ->line('Date: ' . $this->booking->start_time->format('M d, Y'))
            ->line('Time: ' . $this->booking->start_time->format('h:i A'))
            ->line('Staff: ' . ($this->booking->staff ? $this->booking->staff->name : 'Any Staff'))
            ->action('View Appointment', route('customer.bookings.index')) // Assuming this route exists
            ->line('We look forward to seeing you!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'booking_reminder',
            'message' => 'Reminder: You have an appointment for ' . $this->booking->service->name . ' tomorrow at ' . $this->booking->start_time->format('h:i A'),
            'booking_id' => $this->booking->id,
            'service_name' => $this->booking->service->name,
            'date' => $this->booking->start_time->format('Y-m-d'),
            'time' => $this->booking->start_time->format('H:i:s'),
        ];
    }
}
