<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentMissedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $rescheduleUrl = route('student.requests.history');

        return (new MailMessage)
            ->subject('Missed Appointment Notification – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('We noticed that you were unable to attend your scheduled document pickup appointment on **' . $this->appointment->appointment_date->format('F j, Y') . '**.')
            ->line('Because the appointment was missed, your request status has been reset to "Pending".')
            ->line('**Don\'t worry!** You can still claim your documents. Simply log in to your portal and book a new appointment time that works better for you.')
            ->action('Book New Appointment', $rescheduleUrl)
            ->line('Thank you for using CCST DocRequest!')
            ->line('Regards,')
            ->line('CCST Registrar Office');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => '⚠️ You missed your appointment on ' . $this->appointment->appointment_date->format('M d, Y') . '. Please book a new one.',
            'url' => route('student.requests.history', [], false),
            'type' => 'appointment_missed',
            'appointment_id' => $this->appointment->id,
        ];
    }
}
