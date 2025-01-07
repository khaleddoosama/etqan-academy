<?php

namespace App\Listeners;

use App\Events\PaymentApprovedEvent;
use App\Models\User;
use App\Notifications\NotificationContext;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class PaymentApprovedListener
{

    public function handle(PaymentApprovedEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'courseSlug' => $event->getData()['courseSlug'] ?? '',
            'courseTitle' => $event->getData()['courseTitle'] ?? '',
            'payment' => $event->getData()['payment'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->getUsers())->get();


        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");

        $notification = new PaymentApprovedNotification($data['courseSlug'], $data['courseTitle'], $data['payment']);
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($users);


        Log::info("---------------------------------------------");
    }
}
