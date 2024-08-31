<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LectureStatusNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;

    private $lecture_id;
    private $status;
    private $error_message;
    /**
     * Create a new notification instance.
     */
    public function __construct($lecture_id, $status, $error_message = null)
    {
        $this->lecture_id = $lecture_id;
        $this->status = $status;
        $this->error_message = $error_message;
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
        return 'Video Status';
    }

    protected function getMessage()
    {
        $message = $this->status == 1 ? 'Video Published Successfully' : 'Video Failed to Publish' . ' : ' . $this->error_message;
        return $message;
    }

    protected function getType()
    {
        return 'lecture';
    }

    protected function getUrl()
    {
        return route('admin.lectures.edit', $this->lecture_id);
    }

    protected function getIcon()
    {
        return 'fa fa-video';
    }
}
