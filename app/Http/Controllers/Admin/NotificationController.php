<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class NotificationController extends Controller
{
    public function index()
    {
        // Fetch paginated notifications for the authenticated user
        $notifications = auth()->user()->notifications()->paginate(20); // Adjust the number of records per page as needed

        return view('admin.notifications.index', compact('notifications'));
    }

    public function read()
    {
        // Mark all notifications as read for the authenticated user
        auth()->user()->notifications()->update(['read_at' => now()]);

        Toastr::success('All notifications marked as read.', 'Success');


        return redirect()->route('admin.notifications.index');
    }
}
