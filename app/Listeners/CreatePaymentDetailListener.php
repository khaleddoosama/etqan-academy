<?php

namespace App\Listeners;

use App\Events\CreatePaymentDetailEvent;
use App\Models\User;
use App\Notifications\PaymentDetailCreatedNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\NotificationStrategy;
use Illuminate\Support\Facades\Log;

class CreatePaymentDetailListener
{

    public function handle(CreatePaymentDetailEvent $event): void
    {
        Log::info("From " . __CLASS__ . ": ");


        $data = [
            'paymentDetailId' => $event->getData()['paymentDetailId'] ?? '',
        ];

        $admins = User::where('role', 'admin')->permission(['payment_detail.list', 'payment_detail.show'])->get();

        Log::info("admins: " . json_encode($admins));

        Log::info("---------------------------------------------");
        $notification = new PaymentDetailCreatedNotification($data['paymentDetailId']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);
        Log::info("---------------------------------------------");
    }
}
