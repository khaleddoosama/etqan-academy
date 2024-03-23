<?php
// app/Services/AdminNotificationService.php
namespace App\Services;

use App\Models\User;

class AdminNotificationService
{
    public static function notifyAdmins($notification)
    {
        User::where('role', 'admin')->chunk(200, function ($admins) use ($notification) {
            foreach ($admins as $admin) {
                $admin->notify($notification);
            }
        });
    }
}
