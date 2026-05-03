<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestSubmittedNotification extends Notification implements ShouldQueue
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
            ->subject('Document Request Submitted – CCST DocRequest')
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Student') . ',')
            ->line('Your document request has been submitted successfully!')
            ->line('**Reference Number:** ' . $this->docRequest->reference_number)
            ->line('**Total Fee:** ₱' . number_format($this->docRequest->total_fee, 2))
            ->line('Please book an appointment for pickup. Payment will be collected over-the-counter at the cashier\'s office on your appointment day.')
            ->action('View Request Details', $url)
            ->line('Thank you for using CCST DocRequest!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => '📝 Your request ' . $this->docRequest->reference_number . ' has been submitted successfully.',
            'url' => route('student.requests.show', $this->docRequest->id, false),
            'type' => 'request_submitted',
        ];
    }
}
