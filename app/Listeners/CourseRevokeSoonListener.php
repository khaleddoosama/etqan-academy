<?php

namespace App\Listeners;

use App\Events\CourseRevokeSoonEvent;
use App\Models\User;
use App\Notifications\CourseRevokeSoonNotification;
use App\Services\StudentsNotificationService;
use Illuminate\Support\Facades\Log;

class CourseRevokeSoonListener
{
    private $studentsNotificationService;
    public function __construct(StudentsNotificationService $studentsNotificationService)
    {
        $this->studentsNotificationService = $studentsNotificationService;
    }

    public function handle(CourseRevokeSoonEvent $event): void
    {
        Log::info("From" . self::class);


        $data = [
            'course_title' => $event->data['course_title'] ?? '',
            'course_slug' => $event->data['course_slug'] ?? '',
        ];

        Log::info("data: " . json_encode($data));

        $users = User::whereIn('id', $event->users_ids)->get();

        Log::info("users: " . json_encode($users));

        Log::info("---------------------------------------------");
        // $notificationContext = new NotificationContext(new NotificationStrategy());
        // $notificationContext->executeStrategy($data, $users);

        $notification = new CourseRevokeSoonNotification($data['course_slug'], $data['course_title']);

        foreach ($users as $user) {
            $this->studentsNotificationService->notify($notification, $user);
        }

        Log::info("---------------------------------------------");
    }
}
