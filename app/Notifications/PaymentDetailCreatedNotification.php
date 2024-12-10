<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDetailCreatedNotification extends Notification
{
    use Queueable, NotificationToArray;

    public $payment_detail_id;

    public function __construct($payment_detail_id)
    {
        $this->payment_detail_id = $payment_detail_id;
        $this->queue = 'high';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    // get title
    protected function getTitle()
    {
        return 'New Payment Detail';
    }

    protected function getMessage()
    {
        return 'New payment detail created';
    }

    protected function getType()
    {
        return 'payment_detail';
    }

    protected function getUrl()
    {
        return route('admin.payment-details.show', $this->payment_detail_id);
    }

    protected function getIcon()
    {
        return 'fas fa-question-circle';
    }
}
