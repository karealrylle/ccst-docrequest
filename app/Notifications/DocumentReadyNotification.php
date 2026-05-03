<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DocumentReadyNotification extends Notification implements ShouldQueue
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
            ->subject('Your Documents Are Ready for Pickup – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('Your document request (**' . $this->docRequest->reference_number . '**) is now ready for pickup!')
            ->line('Please proceed to the registrar\'s office during office hours to claim your documents.')
            ->line('Don\'t forget to bring your school ID.')
            ->action('View Request Details', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => '📦 Your request ' . $this->docRequest->reference_number . ' is ready for pickup!',
            'url' => route('student.requests.show', $this->docRequest->id, false),
            'type' => 'document_ready',
        ];
    }
}
