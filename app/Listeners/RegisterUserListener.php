<?php

namespace App\Listeners;

use App\Events\RegisterUserEvent;
use App\Models\User;
use App\Notifications\UserRegisteredNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\NotificationStrategy;
use Illuminate\Support\Facades\Log;

class RegisterUserListener
{

    public function handle(RegisterUserEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'userName' => $event->getData()['userName'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        // $users = User::whereIn('id', $event->users_ids)->get();
        $admins = User::where('role', 'admin')->permission(['user.list', 'user.show'])->get();

        Log::info("users: " . json_encode($admins));

        Log::info("---------------------------------------------");

        $notification = new UserRegisteredNotification($data['userName']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);


        Log::info("---------------------------------------------");
    }
}
