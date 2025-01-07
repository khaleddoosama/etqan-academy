<?php

namespace App\Listeners;

use App\Events\ResetPasswordEvent;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class ResetPasswordListener
{

    public function handle(ResetPasswordEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'token' => $event->getData()['token'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->getUsers())->get();

        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");

        $notification = new ResetPasswordNotification($data['token']);
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($users);


        Log::info("---------------------------------------------");
    }
}
