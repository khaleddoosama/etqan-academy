<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Assuming notifications belong to a user (e.g., logged-in user)
        $user = auth()->user();

        // Start by fetching notifications for the user
        $query = $user->notifications();

        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('data->title', 'like', "%{$search}%")
                    ->orWhere('data->message', 'like', "%{$search}%");
            });
        }

        // Apply read filter if provided in created_at
        if ($request->has('daterange')) {
            $read = $request->input('daterange');
            if ($read) {
                $read = explode(' - ', $read);

                $query->whereDate('created_at', '>=', Carbon::parse($read[0]))
                ->whereDate('created_at', '<=', Carbon::parse($read[1]));
            }
        }
        // Paginate the notifications
        $notifications = $query->paginate(10);

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
