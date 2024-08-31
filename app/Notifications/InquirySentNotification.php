<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquirySentNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;

    public $inquiry_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($inquiry_id)
    {
        $this->inquiry_id = $inquiry_id;
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
        return 'New Inquiry';
    }

    protected function getMessage()
    {
        return 'New inquiry received';
    }

    protected function getType()
    {
        return 'inquiry';
    }

    protected function getUrl()
    {
        return route('admin.inquiries.show', $this->inquiry_id);
    }

    protected function getIcon()
    {
        return 'fas fa-question-circle';
    }
}
