<?php

namespace App\Notifications;

use App\Notifications\Strategies\NotificationStrategyInterface;

class NotificationContext
{
    private $strategy;

    public function __construct(NotificationStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function executeStrategy($senders)
    {
        $this->strategy->send($senders);
    }
}
