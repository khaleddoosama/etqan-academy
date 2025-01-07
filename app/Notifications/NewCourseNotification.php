<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCourseNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;

    /**
     * Create a new notification instance.
     */

    private $courseSlug;
    private $courseTitle;
    public function __construct(string $courseSlug, string $courseTitle)
    {
        $this->courseSlug = $courseSlug;
        $this->courseTitle = $courseTitle;
        $this->queue = 'high';  // Explicitly assign to the 'high' queue

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Course Available!')
            ->line('A new course has been created: ' . $this->courseTitle)
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->courseSlug)
            ->line('Thank you for using our application!');
    }

    // get title
    protected function getTitle()
    {
        return 'New Course Available';
    }

    // message
    protected function getMessage()
    {
        return 'A new course has been created: ' . $this->courseTitle;
    }


    protected function getUrl()
    {
        return env('FRONTEND_URL') . 'courses/' . $this->courseSlug;
    }


    protected function getIcon()
    {
        return 'fa fa-plus';
    }
}
