<?php

namespace App\Notifications;

use App\Traits\NotificationToArray;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalRequestNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationToArray;


    private $user_name;
    private $withdrawalRequest_id;
    /**
     * Create a new notification instance.
     */
    public function __construct(string $user_name, int $withdrawalRequest_id)
    {
        $this->user_name = $user_name;
        $this->withdrawalRequest_id = $withdrawalRequest_id;
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
        return 'Withdrawal Request';
    }

    protected function getMessage()
    {
        return $this->user_name . ' has requested withdrawal.';
    }

    protected function getType()
    {
        return 'withdrawal_request';
    }

    protected function getUrl()
    {
        return route('admin.withdrawal_requests.show', $this->withdrawalRequest_id);
    }
}
