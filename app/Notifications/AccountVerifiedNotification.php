<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AccountVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/login');

        return (new MailMessage)
            ->subject('Your Account Has Been Verified – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('Great news! Your account has been verified by the registrar.')
            ->line('You can now log in to the CCST DocRequest System to submit document requests and book appointments.')
            ->action('Login Now', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => '🎉 Your account has been verified! You can now submit document requests.',
            'url' => route('student.dashboard', [], false),
            'type' => 'verification',
        ];
    }
}