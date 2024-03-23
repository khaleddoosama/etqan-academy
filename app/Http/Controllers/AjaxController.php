<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AjaxController extends Controller
{
    //markAsRead
    public function markAsRead(Request $request)
    {
        $id = $request->notification_id;
        auth()->user()->unreadNotifications->where('id', $id)->markAsRead();
        return response()->json(
            [
                'success' => true,
            ]
        );
    }

    //type markAsRead
    public function typeMarkAsRead(Request $request)
    {
        $type = $request->type;
        // auth()->user()->unreadNotifications->where('type', $type)->markAsRead();
        return response()->json(
            [
                'success' => $request->type,
            ]
        );
    }
}
