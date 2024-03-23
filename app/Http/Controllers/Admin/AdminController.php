<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yoeunes\Toastr\Facades\Toastr;

class AdminController extends Controller
{
    //constructor
    // public function __construct()
    // {
    //     //admin.list admin.create admin.edit admin.delete
    //     $this->middleware('permission:admin.list')->only('index');
    //     $this->middleware('permission:admin.create')->only('create', 'store');
    //     $this->middleware('permission:admin.edit')->only('edit', 'update');
    //     $this->middleware('permission:admin.delete')->only('destroy');
    // }

    // home
    public function home()
    {

        return view('admin.home');
    }

    // profile
    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    // update profile
    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update($data);
        Toastr::success(__('messages.user_profile_updated'), __('status.success'));
        return redirect()->back();
    }

    // change password
    public function changePassword(PasswordRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        $user->update(['password' => bcrypt($data['password'])]);
        Toastr::success(__('messages.user_password_updated'), __('status.success'));
        return redirect()->back();
    }

    // All admins
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        return view('admin.admin.index', compact('admins'));
    }

    // create admin
    public function create()
    {
        $roles = Role::get();
        $categories = Category::active()->get();
        return view('admin.admin.create', compact('roles', 'categories'));
    }

    // store admin
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                // 'address' => 'required|string',
                'password' => 'required|min:8|confirmed',
                'role' => 'required|exists:roles,id',
                'category_id' => 'required|exists:categories,id'
            ]);

            $data['password'] = bcrypt($data['password']);
            $data['role'] = 'admin';
            $data['status'] = 1;

            $admin = User::create($data);
            $admin->assignRole($request->role);

            Toastr::success("تم تسجيل المشرف بنجاح", "تمت العملية بنجاح");

            return redirect()->route('admin.all_admin.index');
        } catch (\Exception $e) {
            Toastr::error($e->getMessage(), __('main.Error'));
            return redirect()->back();
        }
    }

    // edit admin
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        $roles = Role::get();
        $categories = Category::active()->get();

        return view('admin.admin.edit', compact('admin', 'roles', 'categories'));
    }

    // update admin
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|email',
            'phone' => 'required',
            'role' => 'required|exists:roles,id'
        ]);

        $admin = User::findOrFail($id);
        unset($data['role']);

        $admin->update($data);

        $admin->syncRoles($request->role);

        Toastr::success("تم تحديث المشرف بنجاح", "تمت العملية بنجاح");

        return redirect()->route('admin.all_admin.index');
    }

    // delete admin
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        Toastr::success("تم حذف المشرف بنجاح", "تمت العملية بنجاح");

        return redirect()->route('admin.all_admin.index');
    }
}
