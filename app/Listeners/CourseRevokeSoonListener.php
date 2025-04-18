<?php

namespace App\Listeners;

use App\Events\CourseRevokeSoonEvent;
use App\Models\User;
use App\Notifications\CourseRevokeSoonNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class CourseRevokeSoonListener
{

    public function handle(CourseRevokeSoonEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'course_title' => $event->getData()['course_title'] ?? '',
            'courseSlug' => $event->getData()['courseSlug'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->getUsers())->get();

        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");

        $notification = new CourseRevokeSoonNotification($data['courseSlug'], $data['course_title']);
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($users);


        Log::info("---------------------------------------------");
    }
}
