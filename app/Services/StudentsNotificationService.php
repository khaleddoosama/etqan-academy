<?php
// app/Services/StudentsNotificationService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;

class StudentsNotificationService
{
    public static function notify($notification, $student)
    {

        Notification::send($student, $notification);
    }

    public static function notifyAll($notification)
    {
        $students = User::getNotifiedStudents();
        Notification::send($students, $notification);
    }
}
