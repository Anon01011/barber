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

        // Send email to staff, employees, salon admins, managers, receptionists, owners
        $isStaffOrAdmin = false;
        try {
            if (method_exists($notifiable, 'hasAnyRole')) {
                $existingRoles = \Spatie\Permission\Models\Role::whereIn('name', [
                    'admin', 'employee', 'staff', 'salon_admin', 'manager', 'receptionist', 'super_admin'
                ])->pluck('name')->toArray();

                $isStaffOrAdmin = !empty($existingRoles) && $notifiable->hasAnyRole($existingRoles);
            }
        } catch (\Exception $e) {
            $isStaffOrAdmin = false;
        }

        // Check if notifiable is the salon owner
        if (!$isStaffOrAdmin && isset($notifiable->id) && $this->booking->salon) {
            if ($this->booking->salon->owner_id == $notifiable->id) {
                $isStaffOrAdmin = true;
            }
        }

        if ($isStaffOrAdmin && !empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $salonSlug = $this->booking->salon->slug
            ?? optional(\App\Models\Salon::find($this->booking->salon_id))->slug
            ?? request()->route('salon_slug')
            ?? 'default';

        $url = route('admin.bookings.index', ['salon_slug' => $salonSlug, 'search' => $this->booking->id]);
        $customerName = $this->booking->customer->name ?? 'Guest';
        $serviceName = $this->booking->service->name ?? 'Service';
        $staffName = $this->booking->staff->name ?? 'Unassigned';
        $date = $this->booking->start_time->format('F j, Y');
        $time = $this->booking->start_time->format('g:i A');
        $isGroup = !empty($this->booking->booking_group_id);

        $subject = 'Booking Update: ' . ucfirst($this->type);
        if ($this->type === 'created') {
            $subject = ($isGroup ? '[Group Booking] ' : '') . 'New Booking Received - ' . $serviceName;
        } elseif ($this->type === 'assigned') {
            $subject = 'New Appointment Assigned to You: ' . $serviceName;
        } elseif ($this->type === 'confirmed') {
            $subject = ($isGroup ? '[Group Booking] ' : '') . 'Booking Confirmed - ' . $serviceName;
        } elseif ($this->type === 'completed') {
            $subject = 'Appointment Completed - ' . $serviceName;
        } elseif ($this->type === 'cancelled') {
            $subject = ($isGroup ? '[Group Booking] ' : '') . 'Booking Cancelled - ' . $serviceName;
        }

        $lineMessage = $this->message;
        if (!$lineMessage) {
            if ($this->type === 'assigned') {
                $lineMessage = "You have been assigned to an appointment for {$customerName}.";
            } elseif ($this->type === 'completed') {
                $lineMessage = "The appointment for {$customerName} ({$serviceName}) has been marked as completed.";
            } elseif ($this->type === 'created') {
                $lineMessage = $isGroup
                    ? "A new group booking has been created for {$customerName}."
                    : "A new appointment has been created for {$customerName}.";
            } else {
                $lineMessage = 'A booking has been updated.';
            }
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($lineMessage)
            ->line("Customer: {$customerName}")
            ->line("Service: {$serviceName}");

        if ($isGroup) {
            $mail->line("Booking Type: Group Booking");
        }

        $mail->line("Assigned Staff: {$staffName}")
            ->line("Date: {$date}")
            ->line("Time: {$time}")
            ->action('View Calendar', $url);

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $salonSlug = $this->booking->salon->slug
            ?? optional(\App\Models\Salon::find($this->booking->salon_id))->slug
            ?? request()->route('salon_slug')
            ?? 'default';

        $isCustomer = false;
        if ($notifiable instanceof \App\Models\User) {
            if ($notifiable->hasRole('customer')) {
                $isCustomer = true;
            } elseif ($this->booking->customer && $this->booking->customer->user_id == $notifiable->id) {
                $isCustomer = true;
            }
        }

        $title = 'Booking Update';
        $icon = 'info'; // info, success, warning, danger

        if ($isCustomer) {
            switch ($this->type) {
                case 'created':
                    $title = 'Appointment Booked Successfully';
                    $icon = 'success';
                    break;
                case 'assigned':
                    $title = 'Stylist Assigned to Your Appointment';
                    $icon = 'primary';
                    break;
                case 'confirmed':
                    $title = 'Appointment Confirmed';
                    $icon = 'success';
                    break;
                case 'completed':
                    $title = 'Thank You for Visiting!';
                    $icon = 'success';
                    break;
                case 'cancelled':
                    $title = 'Appointment Cancelled';
                    $icon = 'danger';
                    break;
                case 'frozen':
                    $title = 'Appointment On Hold';
                    $icon = 'warning';
                    break;
                case 'rescheduled':
                    $title = 'Appointment Rescheduled';
                    $icon = 'warning';
                    break;
                default:
                    $title = 'Appointment Update';
                    $icon = 'info';
                    break;
            }
        } else {
            switch ($this->type) {
                case 'created':
                    $title = 'New Booking Received';
                    $icon = 'info';
                    break;
                case 'assigned':
                    $title = 'Appointment Assigned to You';
                    $icon = 'primary';
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
                case 'frozen':
                    $title = 'Booking Frozen';
                    $icon = 'secondary';
                    break;
                case 'rescheduled':
                    $title = 'Booking Rescheduled';
                    $icon = 'warning';
                    break;
            }
        }

        $customerName = $this->booking->customer->name ?? 'Guest';
        $serviceName = $this->booking->service->name ?? 'Service';
        $staffName = $this->booking->staff->name ?? null;
        $isGroup = !empty($this->booking->booking_group_id);
        $date = $this->booking->start_time->format('F j, Y');
        $time = $this->booking->start_time->format('g:i A');

        // Check for group bookings sharing this group ID
        $groupBookings = collect();
        if ($isGroup) {
            $groupBookings = \App\Models\Booking::where('booking_group_id', $this->booking->booking_group_id)
                ->where('salon_id', $this->booking->salon_id)
                ->with(['service', 'staff'])
                ->orderBy('start_time', 'asc')
                ->get();
        }

        $groupCount = $groupBookings->count();
        $isMultiServiceGroup = $groupCount > 1;

        if ($isCustomer) {
            $staffHtml = $staffName
                ? "<span class='fw-semibold text-dark'>" . e($staffName) . "</span>"
                : "<span class='text-muted small'>Any Available Stylist</span>";
        } else {
            $staffHtml = $staffName
                ? "<span class='fw-semibold text-dark'>" . e($staffName) . "</span>"
                : "<span class='badge bg-warning text-dark' style='font-size:0.72rem;'><i class='fas fa-exclamation-triangle me-1'></i>Unassigned</span>";
        }

        $salonName = $this->booking->salon->name ?? 'Salon';
        $headerRowHtml = $isCustomer
            ? "<tr><td class='py-1 text-muted' style='width:38%;'>Salon</td><td class='py-1 fw-semibold text-dark'>" . e($salonName) . "</td></tr>"
            : "<tr><td class='py-1 text-muted' style='width:38%;'>Customer</td><td class='py-1 fw-semibold text-dark'>" . e($customerName) . "</td></tr>";

        // Build clean minimal detail message
        if ($isMultiServiceGroup) {
            $groupTotal = $groupBookings->sum('amount');
            $formattedTotal = format_currency($groupTotal);
            $salonTz = salon_timezone();

            $servicesRowsHtml = '';
            foreach ($groupBookings as $idx => $gb) {
                $gbSvcName = e($gb->service->name ?? 'Service');
                $gbTime = $gb->start_time ? $gb->start_time->copy()->timezone($salonTz)->format('g:i A') : '';
                if ($isCustomer) {
                    $gbStaff = $gb->staff
                        ? "<span class='fw-semibold text-dark'>" . e($gb->staff->name) . "</span>"
                        : "<span class='text-muted small'>Any Available Stylist</span>";
                } else {
                    $gbStaff = $gb->staff
                        ? "<span class='fw-semibold text-dark'>" . e($gb->staff->name) . "</span>"
                        : "<span class='badge bg-warning text-dark' style='font-size:0.72rem;'><i class='fas fa-exclamation-triangle me-1'></i>Unassigned</span>";
                }
                $gbPrice = format_currency($gb->amount);
                $rowBg = ($idx % 2 === 0) ? '' : "style='background:#f8f9fa;'";

                $servicesRowsHtml .= "
                    <tr {$rowBg}>
                        <td class='py-2 ps-3 fw-semibold text-dark' style='font-size:0.82rem;'>" . ($idx + 1) . ". {$gbSvcName}</td>
                        <td class='py-2 text-muted' style='font-size:0.82rem;'>{$gbTime}</td>
                        <td class='py-2' style='font-size:0.82rem;'>{$gbStaff}</td>
                        <td class='py-2 pe-3 text-end fw-bold text-dark' style='font-size:0.82rem;'>{$gbPrice}</td>
                    </tr>
                ";
            }

            $customMsgHtml = $this->message ? "<p class='text-muted small mb-3'>" . e($this->message) . "</p>" : "";

            $detailMessage = "
                <div class='text-start' style='font-family:inherit;'>
                    {$customMsgHtml}
                    <table class='w-100 mb-3' style='border-collapse:collapse;font-size:0.85rem;'>
                        {$headerRowHtml}
                        <tr>
                            <td class='py-1 text-muted'>Date</td>
                            <td class='py-1 fw-semibold text-dark'>{$date}</td>
                        </tr>
                        <tr>
                            <td class='py-1 text-muted'>Total</td>
                            <td class='py-1 fw-semibold text-dark'>{$formattedTotal}</td>
                        </tr>
                    </table>
                    <div class='pt-2 border-top'>
                        <p class='text-muted mb-2' style='font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;'><i class='fas fa-users me-1'></i> Group &mdash; {$groupCount} Services</p>
                        <div class='rounded-2 border' style='overflow:hidden;'>
                            <table class='w-100' style='border-collapse:collapse;'>
                                <thead style='background:#f1f3f5;'>
                                    <tr>
                                        <th class='ps-3 py-2 text-muted fw-normal' style='font-size:0.72rem;text-transform:uppercase;'>Service</th>
                                        <th class='py-2 text-muted fw-normal' style='font-size:0.72rem;text-transform:uppercase;'>Time</th>
                                        <th class='py-2 text-muted fw-normal' style='font-size:0.72rem;text-transform:uppercase;'>Stylist</th>
                                        <th class='pe-3 py-2 text-muted fw-normal text-end' style='font-size:0.72rem;text-transform:uppercase;'>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$servicesRowsHtml}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            ";
        } else {
            $groupBadgeHtml = $isGroup
                ? "<tr><td class='py-1 text-muted'>Type</td><td class='py-1'><span class='badge' style='background:#7c3aed;color:#fff;font-size:0.72rem;'>Group Booking</span></td></tr>"
                : "";

            $customMsgHtml = $this->message ? "<p class='text-muted small mb-3'>" . e($this->message) . "</p>" : "";

            $detailMessage = "
                <div class='text-start' style='font-family:inherit;'>
                    {$customMsgHtml}
                    <table class='w-100' style='border-collapse:collapse;font-size:0.85rem;'>
                        {$groupBadgeHtml}
                        {$headerRowHtml}
                        <tr>
                            <td class='py-1 text-muted'>Service</td>
                            <td class='py-1 fw-semibold text-dark'>" . e($serviceName) . "</td>
                        </tr>
                        <tr>
                            <td class='py-1 text-muted'>Stylist</td>
                            <td class='py-1'>{$staffHtml}</td>
                        </tr>
                        <tr>
                            <td class='py-1 text-muted'>Date</td>
                            <td class='py-1 fw-semibold text-dark'>{$date}</td>
                        </tr>
                        <tr>
                            <td class='py-1 text-muted'>Time</td>
                            <td class='py-1 fw-semibold text-dark'>{$time}</td>
                        </tr>
                    </table>
                </div>
            ";
        }

        if ($isCustomer) {
            $actionLink = route('customer.appointments.show', [
                'salon_slug' => $salonSlug,
                'booking' => $this->booking->id
            ]);
        } else {
            $actionLink = route('admin.bookings.index', [
                'salon_slug' => $salonSlug,
                'booking_id' => $this->booking->id
            ]);
        }

        return [
            'booking_id'      => $this->booking->id,
            'is_group_booking' => $isGroup,
            'title'           => $title . ($isGroup ? ' (Group)' : ''),
            'message'         => $detailMessage,
            'type'            => $icon,
            'link'            => $actionLink,
        ];
    }
}
