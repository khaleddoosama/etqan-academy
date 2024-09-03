<?php
// app/Services/StudentsNotificationService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

class StudentsNotificationService
{
    public static function notify($notification)
    {
        // User::admin()->chunk(200, function ($admins) use ($notification) {
        //     foreach ($admins as $admin) {
        //         $admin->notify($notification);
        //     }
        // });

        $students = User::getNotifiedStudents();
        Notification::send($students, $notification);
    }
}
