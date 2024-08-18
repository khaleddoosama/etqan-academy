<?php


namespace App\Traits;
trait NotificationToArray
{
    /**
     * Convert the notification into an array.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'type' => $this->getType(),
            'data' => $this->getData(),
            'action' => $this->getUrl(),
            'icon' => $this->getIcon(),
        ];
    }


    /**
     * Get the notification title.
     *
     * @return string
     */
    protected function getTitle()
    {
        return 'Notification';
    }

    /**
     * Get the notification message.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'You have a new notification.';
    }

    /**
     * Get the notification type.
     *
     * @return string
     */
    protected function getType()
    {
        return 'info';
    }

    /**
     * Get the notification URL.
     *
     * @return string
     */
    protected function getUrl()
    {
        return '/';
    }

    /**
     * Get any additional data for the notification.
     *
     * @return array
     */
    protected function getData()
    {
        return [];
    }

    /**
     * Get the notification icon.
     *
     * @return string
     */
    protected function getIcon()
    {
        return 'fas fa-bell';
    }
}
