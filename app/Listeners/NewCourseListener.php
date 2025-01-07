<?php

namespace App\Listeners;

use App\Events\NewCourseEvent;
use App\Models\User;
use App\Notifications\NewCourseNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\EmailStrategy;
use Illuminate\Support\Facades\Log;

class NewCourseListener
{

    public function handle(NewCourseEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'course_slug' => $event->getData()['course_slug'] ?? '',
            'course_title' => $event->getData()['course_title'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        // $users = User::whereIn('id', $event->getUsers())->get();
        $students = User::getNotifiedStudents();


        Log::info("students: " . json_encode($students));

        Log::info("---------------------------------------------");

        $notification = new NewCourseNotification($data['course_slug'], $data['course_title']);
        $emailStrategy = new EmailStrategy($notification);
        $notificationContext = new NotificationContext($emailStrategy);
        $notificationContext->executeStrategy($students);


        Log::info("---------------------------------------------");
    }
}
