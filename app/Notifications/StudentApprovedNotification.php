<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class StudentApprovedNotification extends Notification
{
    use Queueable, NotificationToArray;

    private $courseSlug;
    private $title;
    public function __construct(string $courseSlug, string $title)
    {
        $this->courseSlug = $courseSlug;
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
            ->subject('Access Approved: New Course Available!')
            ->line('Congratulations! You have been approved to access the course: ' . $this->title)
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->courseSlug)
            ->line('We hope you enjoy the learning experience!');
    }

    // get title
    protected function getTitle()
    {
        return 'Access Approved: New Course Available';
    }

    // message
    protected function getMessage()
    {
        return 'Congratulations! You have been approved to access the course: ' . $this->title;
    }


    // get url
    protected function getUrl()
    {
        return env('FRONTEND_URL') . 'courses/' . $this->courseSlug;
    }

    // get icon
    protected function getIcon()
    {
        return 'fa fa-check';
    }
}
