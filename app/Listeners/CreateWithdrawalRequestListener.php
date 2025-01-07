<?php

namespace App\Listeners;

use App\Events\CreateWithdrawalRequestEvent;
use App\Models\User;
use App\Notifications\WithdrawalRequestNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\NotificationStrategy;
use Illuminate\Support\Facades\Log;

class CreateWithdrawalRequestListener
{

    public function handle(CreateWithdrawalRequestEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'userName' => $event->getData()['userName'] ?? '',
            'withdrawalRequestId' => $event->getData()['withdrawalRequestId'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        // $users = User::whereIn('id', $event->users_ids)->get();
        $admins = User::where('role', 'admin')->permission(['withdrawal.list', 'withdrawal.show'])->get();

        Log::info("users: " . json_encode($admins));

        Log::info("---------------------------------------------");

        $notification = new WithdrawalRequestNotification($data['userName'], $data['withdrawalRequestId']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);


        Log::info("---------------------------------------------");
    }
}
