<?php

namespace App\Listeners;

use App\Events\CourseRequestEvent;
use App\Models\User;
use App\Notifications\CourseRequestNotification;
use App\Notifications\NotificationContext;
use App\Notifications\Strategies\NotificationStrategy;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CourseRequestListener
{

    public function handle(CourseRequestEvent $event): void
    {
        Log::info("From " . __CLASS__ . ": ");


        $data = [
            'student_name' => $event->data['student_name'] ?? '',
            'course_request_id' => $event->data['course_request_id'] ?? '',
        ];

        // $users = User::whereIn('id', $event->users_ids)->get();
        $admins = User::where('role', 'admin')->permission(['request_course.list', 'request_course.show'])->get();

        Log::info("admins: " . json_encode($admins));

        Log::info("---------------------------------------------");
        $notification = new CourseRequestNotification($data['student_name'], $data['course_request_id']);
        $notificationStrategy = new NotificationStrategy($notification);
        $notificationContext = new NotificationContext($notificationStrategy);
        $notificationContext->executeStrategy($admins);
        Log::info("---------------------------------------------");
    }
}
