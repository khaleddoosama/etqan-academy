<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CourseRevokeSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $courseSlug;
    private $course_title;

    public function __construct(string $courseSlug, string $course_title)
    {
        $this->courseSlug = $courseSlug;
        $this->course_title = $course_title;
        $this->queue = 'high';
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
            ->subject('Course Revoke Soon: ' . $this->course_title)
            ->line('The course ' . $this->course_title . ' is about to end soon.')
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->courseSlug)
            ->line('Please renew your access to continue accessing the course.');
    }
}
