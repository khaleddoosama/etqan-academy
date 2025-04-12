<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentOpinionCreateNotification extends Notification
{
    use Queueable, NotificationToArray;

    private $userName;

    public function __construct(string $userName)
    {
        $this->userName = $userName;
        $this->queue = 'high';  // Explicitly assign to the 'high' queue

    }

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
        return 'New Student Opinion';
    }

    protected function getMessage()
    {
        return $this->userName . ' has submitted a new student opinion.';
    }

    protected function getType()
    {
        return 'student_opinion';
    }

    protected function getUrl()
    {
        return route('admin.student-opinions.index');
    }

    protected function getIcon()
    {
        return 'fas fa-question-circle';
    }
}
