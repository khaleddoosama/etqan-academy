<?php

namespace App\Listeners;

use App\Events\CreateStudentOpinionEventEvent;
use App\Models\User;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\NotificationStrategy;
use App\Notifications\StudentOpinionCreateNotification;
use Illuminate\Support\Facades\Log;

class CreateStudentOpinionEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateStudentOpinionEventEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'userName' => $event->getData()['userName'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        // $users = User::whereIn('id', $event->users_ids)->get();
        $admins = User::where('role', 'admin')->permission(['student_opinion.list', 'student_opinion.status'])->get();

        Log::info("users: " . json_encode($admins));

        Log::info("---------------------------------------------");

        $notification = new StudentOpinionCreateNotification($data['userName']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);


        Log::info("---------------------------------------------");
    }
}
