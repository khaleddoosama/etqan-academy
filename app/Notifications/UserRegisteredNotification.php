<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;


    private $user_name;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $user_name)
    {
        $this->user_name = $user_name;
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
        return 'New User Registration';
    }

    protected function getMessage()
    {
        return $this->user_name . ' has registered on the platform.';
    }

    protected function getType()
    {
        return 'user_registration';
    }

    protected function getUrl()
    {
        return route('admin.users.active');
    }

    protected function getIcon()
    {
        return 'fas fa-user-plus';
    }
}
