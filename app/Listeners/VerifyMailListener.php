<?php

namespace App\Listeners;

use App\Events\VerifyMailEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class VerifyMailListener
{

    public function handle(VerifyMailEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->getUsers())->get();

        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");

        $notification = new CustomVerifyEmail();
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($users);


        Log::info("---------------------------------------------");
    }
}
