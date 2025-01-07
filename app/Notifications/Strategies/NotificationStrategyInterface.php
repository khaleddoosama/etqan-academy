<?php

namespace App\Notifications\Strategies;

interface NotificationStrategyInterface
{
    public function send($senders) : void;
}
