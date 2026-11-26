<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RatingReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $rating;
    public $booking;

    public function __construct(Rating $rating, Booking $booking)
    {
        $this->rating = $rating;
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $stars = str_repeat('★', (int) $this->rating->rating) . str_repeat('☆', 5 - (int) $this->rating->rating);
        $customerName = $this->booking->customer->name ?? 'A customer';
        $serviceName  = $this->booking->service->name ?? 'Service';
        $comment      = $this->rating->comment ?? null;

        $salonSlug = $this->booking->salon->slug
            ?? optional(\App\Models\Salon::find($this->booking->salon_id))->slug
            ?? 'default';

        $link = route('admin.bookings.index', [
            'salon_slug' => $salonSlug,
            'booking_id' => $this->booking->id,
        ]);

        $message = "{$customerName} rated '{$serviceName}' {$stars} ({$this->rating->rating}/5)";
        if ($comment) {
            $message .= ": \"{$comment}\"";
        }

        return [
            'booking_id'  => $this->booking->id,
            'rating_id'   => $this->rating->id,
            'title'       => 'New Rating Received',
            'message'     => $message,
            'type'        => 'success',
            'link'        => $link,
        ];
    }
}
