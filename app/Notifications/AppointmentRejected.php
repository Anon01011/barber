<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class AppointmentRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $staffName;
    protected $reason;

    public function __construct(Booking $booking, $staffName, $reason = null)
    {
        $this->booking = $booking;
        $this->staffName = $staffName;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $role = $notifiable->roles->first()->name ?? '';
        
        if ($role === 'customer') {
            $message = (new MailMessage)
                ->subject('Appointment Rejected')
                ->greeting('Hello ' . $notifiable->name)
                ->line('Your appointment has been rejected by ' . $this->staffName)
                ->line('Service: ' . $this->booking->service->name)
                ->line('Date: ' . $this->booking->start_time->format('M d, Y'))
                ->line('Time: ' . $this->booking->start_time->format('h:i A'));

            if ($this->reason) {
                $message->line('Reason: ' . $this->reason);
            }

            return $message->action('Book New Appointment', route('customer.appointments.create'))
                         ->line('We apologize for any inconvenience. Please try booking another time.');
        } elseif (in_array($role, ['super_admin', 'salon_admin', 'manager'])) {
            $message = (new MailMessage)
                ->subject('Appointment Rejected by Staff')
                ->greeting('Hello ' . $notifiable->name)
                ->line('An appointment has been rejected by ' . $this->staffName)
                ->line('Customer: ' . $this->booking->customer->name)
                ->line('Service: ' . $this->booking->service->name)
                ->line('Date: ' . $this->booking->start_time->format('M d, Y'))
                ->line('Time: ' . $this->booking->start_time->format('h:i A'));

            if ($this->reason) {
                $message->line('Reason: ' . $this->reason);
            }

            return $message->action('View Appointment', route('admin.bookings.show', $this->booking->id))
                         ->line('Please review this rejection and take appropriate action if necessary.');
        }
    }

    public function toArray($notifiable)
    {
        $role = $notifiable->roles->first()->name ?? '';
        
        if ($role === 'customer') {
            return [
                'type' => 'appointment_rejected',
                'message' => 'Your appointment for ' . $this->booking->service->name . ' has been rejected by ' . 
                           $this->staffName . ' on ' . $this->booking->start_time->format('M d, Y') . ' at ' . 
                           $this->booking->start_time->format('h:i A') . 
                           ($this->reason ? ' (Reason: ' . $this->reason . ')' : ''),
                'booking_id' => $this->booking->id,
                'staff_name' => $this->staffName,
                'service_name' => $this->booking->service->name,
                'date' => $this->booking->start_time->format('Y-m-d'),
                'time' => $this->booking->start_time->format('H:i:s'),
                'reason' => $this->reason,
            ];
        } elseif (in_array($role, ['super_admin', 'salon_admin', 'manager'])) {
            return [
                'type' => 'appointment_rejected',
                'message' => 'Appointment for ' . $this->booking->customer->name . ' has been rejected by ' . 
                           $this->staffName . ' for ' . $this->booking->service->name . ' on ' . 
                           $this->booking->start_time->format('M d, Y') . ' at ' . 
                           $this->booking->start_time->format('h:i A') . 
                           ($this->reason ? ' (Reason: ' . $this->reason . ')' : ''),
                'booking_id' => $this->booking->id,
                'customer_name' => $this->booking->customer->name,
                'staff_name' => $this->staffName,
                'service_name' => $this->booking->service->name,
                'date' => $this->booking->start_time->format('Y-m-d'),
                'time' => $this->booking->start_time->format('H:i:s'),
                'reason' => $this->reason,
            ];
        }
    }
}