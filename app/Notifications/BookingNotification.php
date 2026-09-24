<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification
{
    use Queueable;

    public $booking;
    public $type; // 'created', 'updated', 'rescheduled', 'cancelled'
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, string $type, string $message = null)
    {
        $this->booking = $booking;
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Send email to staff (employees, admins, owners)
        // Customers already get BookingStatusUpdatedMail via BookingController
        if (method_exists($notifiable, 'hasRole') && $notifiable->hasRole(['employee', 'admin', 'owner', 'super_admin'])) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('admin.bookings.index');
        $customerName = $this->booking->customer->name ?? 'Guest';
        $serviceName = $this->booking->service->name ?? 'Service';
        $date = $this->booking->start_time->format('F j, Y');
        $time = $this->booking->start_time->format('g:i A');

        $subject = 'Booking Update: ' . ucfirst($this->type);
        if ($this->type === 'created') {
            $subject = 'New Booking Received';
        } elseif ($this->type === 'confirmed') {
            $subject = 'Booking Confirmed';
        } elseif ($this->type === 'cancelled') {
            $subject = 'Booking Cancelled';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message ?? 'A booking has been updated.')
            ->line("Customer: {$customerName}")
            ->line("Service: {$serviceName}")
            ->line("Date: {$date}")
            ->line("Time: {$time}")
            ->action('View Calendar', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $title = 'Booking Update';
        $icon = 'info'; // info, success, warning, danger

        switch ($this->type) {
            case 'created':
                $title = 'New Booking Received';
                $icon = 'info';
                break;
            case 'confirmed':
                $title = 'Booking Confirmed';
                $icon = 'success';
                break;
            case 'completed':
                $title = 'Booking Completed';
                $icon = 'success';
                break;
            case 'cancelled':
                $title = 'Booking Cancelled';
                $icon = 'danger';
                break;
            case 'rescheduled':
                $title = 'Booking Rescheduled';
                $icon = 'warning';
                break;
        }

        $customerName = $this->booking->customer->name ?? 'Guest';
        $serviceName = $this->booking->service->name ?? 'Service';
        $date = $this->booking->start_time->format('F j, Y');
        $time = $this->booking->start_time->format('g:i A');

        $detailMessage = "
            <div class='text-start'>
                <div class='p-3 bg-light rounded-3 border mb-3'>
                    <div class='d-flex align-items-center mb-2'>
                        <div class='icon-shape icon-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center' style='width: 32px; height: 32px;'>
                            <i class='fas fa-user fa-xs'></i>
                        </div>
                        <div>
                            <small class='text-muted d-block' style='font-size: 0.75rem; line-height: 1;'>Customer</small>
                            <span class='fw-bold text-dark'>{$customerName}</span>
                        </div>
                    </div>
                    <div class='d-flex align-items-center mb-2'>
                        <div class='icon-shape icon-sm bg-info text-white rounded-circle me-2 d-flex align-items-center justify-content-center' style='width: 32px; height: 32px;'>
                            <i class='fas fa-cut fa-xs'></i>
                        </div>
                        <div>
                            <small class='text-muted d-block' style='font-size: 0.75rem; line-height: 1;'>Service</small>
                            <span class='fw-bold text-dark'>{$serviceName}</span>
                        </div>
                    </div>
                    <div class='row g-0'>
                        <div class='col-6'>
                            <div class='d-flex align-items-center'>
                                <div class='icon-shape icon-sm bg-warning text-dark rounded-circle me-2 d-flex align-items-center justify-content-center' style='width: 32px; height: 32px;'>
                                    <i class='fas fa-calendar-alt fa-xs'></i>
                                </div>
                                <div>
                                    <small class='text-muted d-block' style='font-size: 0.75rem; line-height: 1;'>Date</small>
                                    <span class='fw-bold text-dark'>{$date}</span>
                                </div>
                            </div>
                        </div>
                        <div class='col-6'>
                            <div class='d-flex align-items-center'>
                                <div class='icon-shape icon-sm bg-success text-white rounded-circle me-2 d-flex align-items-center justify-content-center' style='width: 32px; height: 32px;'>
                                    <i class='fas fa-clock fa-xs'></i>
                                </div>
                                <div>
                                    <small class='text-muted d-block' style='font-size: 0.75rem; line-height: 1;'>Time</small>
                                    <span class='fw-bold text-dark'>{$time}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ";

        return [
            'booking_id' => $this->booking->id,
            'title' => $title,
            'message' => $this->message ?? $detailMessage,
            'type' => $icon,
            'link' => route('admin.bookings.show', $this->booking->id),
        ];
    }
}
