<?php
// app/Services/AdminNotificationService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    public static function notifyAdmins($notification, $permissions = [])
    {
        $adminsWithPermissions = User::admin()->permission($permissions)->get();

        Notification::send($adminsWithPermissions, $notification);
    }
}
