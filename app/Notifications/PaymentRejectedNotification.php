<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentRejectedNotification extends Notification
{
    use Queueable;

    private $course_slug;
    private $cours_title;

    public function __construct(string $course_slug, string $cours_title)
    {
        $this->course_slug = $course_slug;
        $this->cours_title = $cours_title;
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
            ->subject('Access Rejected: ' . $this->cours_title)
            ->line('Sorry, your payment for the course ' . $this->cours_title . ' has been rejected.')
            ->line('Please contact support for more information.')
            ->line('Your invoice is attached below.');
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
