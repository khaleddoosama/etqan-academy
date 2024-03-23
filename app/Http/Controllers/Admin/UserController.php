<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class UserController extends Controller
{
    private $userService;
    // constructor for UserService
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    //pending
    public function pending()
    {
        $users = $this->userService->getPendingUsers();
        $title = __('attributes.users_pending');
        return view('admin.user.index', compact('users', 'title'));
    }

    //active
    public function active()
    {
        $users = $this->userService->getActiveUsers();

        $title = __('attributes.users_active');

        return view('admin.user.index', compact('users', 'title'));
    }

    //inactive
    public function inactive()
    {
        $users = $this->userService->getInactiveUsers();
        $title = __('attributes.users_inactive');
        return view('admin.user.index', compact('users', 'title'));
    }

    //edit
    public function edit(User $user)
    {
        return view('admin.user.edit', compact('user'));
    }

    //update
    public function update(ProfileRequest $request, User $user)
    {
        $data = $request->validated();

        // $user->update($data);
        $this->userService->updateUser($data, $user) ?
            Toastr::success(__('messages.user_updated'), __('status.success')) : '';

        return redirect()->back();
    }

    //change password
    public function updatePassword(PasswordRequest $request, User $user)
    {
        $data = $request->validated();


        $this->userService->updateUser($data, $user) ? Toastr::success(__('messages.user_password_updated'), __('status.success')) : '';

        return redirect()->back();
    }

    //status
    public function status(Request $request, User $user)
    {
        $data = $request->validate([
            'status' => 'required',
        ]);
        
        $this->userService->updateUser(['status' => $request->status], $user) ? Toastr::success(__('messages.user_status_updated'), __('status.success')) : '';

        return redirect()->back();
    }
}
