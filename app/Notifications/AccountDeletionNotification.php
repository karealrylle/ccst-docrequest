<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeletionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $student)
    {
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): array|MailMessage
    {
        return (new MailMessage)
                    ->subject('Account Deletion Request - CCST DocRequest')
                    ->greeting('Hello Registrar,')
                    ->line('A student has requested to delete their account.')
                    ->line('**Student Name:** ' . $this->student->full_name)
                    ->line('**Email:** ' . $this->student->email)
                    ->line('The account has been soft-deleted and will be permanently removed in 30 days.')
                    ->action('View Student Details', route('registrar.students.index'))
                    ->line('If this was a mistake, you can restore the account within the grace period.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "🗑️ Account deletion requested by {$this->student->full_name}.",
            'student_id' => $this->student->id,
            'action_url' => route('registrar.students.index'),
        ];
    }
}
