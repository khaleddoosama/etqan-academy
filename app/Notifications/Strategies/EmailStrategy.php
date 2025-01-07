<?php

namespace App\Notifications\Strategies;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EmailStrategy implements NotificationStrategyInterface
{
    // constructor

    private Notification $notification;
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function send($senders): void
    {
        Log::info("From Email Strategy: ");

        foreach ($senders as $sender) {
            $sender->notify($this->notification);
        }

        Log::info("Finish EmailStrategy");
    }
}
