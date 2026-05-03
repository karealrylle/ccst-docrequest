<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class WalkInDocumentReadyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $docRequest;

    public function __construct(DocumentRequest $docRequest)
    {
        $this->docRequest = $docRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail']; // Only send via mail since walk-ins have no DB account
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Documents Are Ready for Pickup – CCST DocRequest')
            ->greeting('Hello ' . $this->docRequest->full_name . ',')
            ->line('Your document request (**' . $this->docRequest->reference_number . '**) is now ready for pickup!')
            ->line('**Pickup Schedule:** You may claim your documents starting today during our office hours (Monday to Friday, 8:00 AM - 5:00 PM).')
            ->line('**Instructions on how to proceed:**')
            ->line('1. Proceed directly to the **Cashier** to pay for your requested documents. Your payment slip has already been forwarded to them.')
            ->line('2. After payment, present your **Proof of Payment (Receipt)** to the **Registrar\'s Office**.')
            ->line('3. Receive your requested documents.')
            ->line('Please bring a valid ID for verification.')
            ->line('Thank you for your patience and for using CCST DocRequest!');
    }
}
