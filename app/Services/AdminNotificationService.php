<?php
// app/Services/AdminNotificationService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    public static function notifyAdmins($notification)
    {
        // User::admin()->chunk(200, function ($admins) use ($notification) {
        //     foreach ($admins as $admin) {
        //         $admin->notify($notification);
        //     }
        // });

       $admins = Cache::remember('admins', 60, function () {
            return User::admin()->get();
        });
        Notification::send($admins, $notification);
    }
}
