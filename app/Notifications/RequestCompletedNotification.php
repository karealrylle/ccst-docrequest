<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $docRequest;

    public function __construct(DocumentRequest $docRequest)
    {
        $this->docRequest = $docRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/student/requests/' . $this->docRequest->id);

        return (new MailMessage)
            ->subject('Your Document Request Has Been Completed – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('Your document request (**' . $this->docRequest->reference_number . '**) has been completed and received.')
            ->line('If you need any additional documents in the future, you can submit a new request through the system.')
            ->action('View Request Details', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => '✅ Your request ' . $this->docRequest->reference_number . ' has been completed.',
            'url' => route('student.requests.show', $this->docRequest->id, false),
            'type' => 'request_completed',
        ];
    }
}
