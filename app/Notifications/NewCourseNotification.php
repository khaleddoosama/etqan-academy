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

    private $course_slug;
    private $title;
    public function __construct(string $course_slug, string $title)
    {
        $this->course_slug = $course_slug;
        $this->title = $title;
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
            ->line('A new course has been created: ' . $this->title)
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->course_slug)
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
        return 'A new course has been created: ' . $this->title;
    }


    protected function getUrl()
    {
        return env('FRONTEND_URL') . 'courses/' . $this->course_slug;
    }


    protected function getIcon()
    {
        return 'fa fa-plus';
    }
}
