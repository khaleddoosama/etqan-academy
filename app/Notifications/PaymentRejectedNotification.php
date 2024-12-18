<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Access Rejected: ' . $this->course_title)
            ->line('Sorry, your payment for the course ' . $this->course_title . ' has been rejected.')
            ->line('Please contact support for more information.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
