<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\PasswordRequest;
use App\Services\CategoryService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Yoeunes\Toastr\Facades\Toastr;

class UserController extends Controller
{
    private $userService;
    private $categoryService;
    private $genders;
    // constructor for UserService
    public function __construct(UserService $userService, CategoryService $categoryService)
    {
        $this->genders = ['Male', 'Female'];

        $this->userService = $userService;
        $this->categoryService = $categoryService;
        $this->middleware('permission:user.list')->only('active', 'inactive');
        $this->middleware('permission:user.show')->only('show');
        $this->middleware('permission:user.edit')->only('edit', 'update', 'updatePassword');
        $this->middleware('permission:user.status')->only('status');
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

    // create
    public function create()
    {
        $categories = $this->categoryService->getCategories();
        $genders = $this->genders;
        return view('admin.user.create', compact('categories', 'genders'));
    }

    //store
    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = $this->userService->createUser($data);

        $user->sendEmailVerificationNotification();


        Toastr::success(__('messages.user_created'), __('status.success'));
        return redirect()->route('admin.users.active');
    }


    //edit
    public function edit($id)
    {
        $user = $this->userService->getUser($id);
        $categories = $this->categoryService->getCategories();

        $genders = $this->genders;

        return view('admin.user.edit', compact('user', 'categories', 'genders'));
    }

    //update
    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userService->getUser($id);

        // $user->update($data);
        $this->userService->updateUser($data, $user) ?
            Toastr::success(__('messages.user_updated'), __('status.success')) : '';

        return redirect()->back();
    }

    //change password
    public function updatePassword(PasswordRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userService->getUser($id);

        $this->userService->updateUser($data, $user) ? Toastr::success(__('messages.user_password_updated'), __('status.success')) : '';

        return redirect()->back();
    }

    //status
    public function status(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required',
        ]);

        $user = $this->userService->getUser($id);

        $this->userService->updateUser(['status' => $request->status], $user) ? Toastr::success(__('messages.user_status_updated'), __('status.success')) : '';

        return redirect()->back();
    }
}
