<?php

namespace App\Listeners;

use App\Events\SentInquiryEvent;
use App\Models\User;
use App\Notifications\InquirySentNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use App\Notifications\Strategies\NotificationStrategy;
use Illuminate\Support\Facades\Log;

class SentInquiryListener
{

    public function handle(SentInquiryEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'inquiry_id' => $event->getData()['inquiry_id'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        // $users = User::whereIn('id', $event->users_ids)->get();
        $admins = User::where('role', 'admin')->permission(['inquiry.list', 'inquiry.show'])->get();

        Log::info("users: " . json_encode($admins));

        Log::info("---------------------------------------------");

        $notification = new InquirySentNotification($data['inquiry_id']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);


        Log::info("---------------------------------------------");
    }
}
