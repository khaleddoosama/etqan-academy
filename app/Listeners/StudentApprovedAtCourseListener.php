<?php

namespace App\Listeners;

use App\Events\StudentApprovedAtCourseEvent;
use App\Models\User;
use App\Notifications\StudentApprovedAtCourseNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class StudentApprovedAtCourseListener
{

    public function handle(StudentApprovedAtCourseEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'courseSlug' => $event->getData()['courseSlug'] ?? '',
            'courseTitle' => $event->getData()['courseTitle'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->getUsers())->get();


        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");

        $notification = new StudentApprovedAtCourseNotification($data['courseSlug'], $data['courseTitle']);
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($users);


        Log::info("---------------------------------------------");
    }
}
