<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseRequestNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;


    private $student_name;
    private $course_request_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $student_name, int $course_request_id)
    {
        $this->student_name = $student_name;
        $this->course_request_id = $course_request_id;

        $this->queue = 'high';  // Explicitly assign to the 'high' queue

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
        return 'New Course Request';
    }

    protected function getMessage()
    {
        return 'New course request from ' . $this->student_name;
    }

    protected function getType()
    {
        return 'course_request';
    }

    protected function getUrl()
    {
        return route('admin.request_courses.show', $this->course_request_id);
    }

    protected function getIcon()
    {
        return 'fas fa-book';
    }
}
