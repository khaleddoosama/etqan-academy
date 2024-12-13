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

    private $course_slug;
    private $course_title;

    public function __construct(string $course_slug, string $course_title)
    {
        $this->course_slug = $course_slug;
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
            ->action('View Course', env('FRONTEND_URL') . 'courses/' . $this->course_slug)
            ->line('Please renew your access to continue accessing the course.');
    }
}
