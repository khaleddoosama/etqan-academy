<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminRequest;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Services\AdminService;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Yoeunes\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{

    protected $adminService;
    protected $dashboardService;
    //constructor
    public function __construct(AdminService $adminService, DashboardService $dashboardService)
    {
        //admin.list admin.create admin.edit admin.delete
        $this->middleware('permission:admin.list')->only('index');
        $this->middleware('permission:admin.create')->only('create', 'store');
        $this->middleware('permission:admin.edit')->only('edit', 'update');
        $this->middleware('permission:admin.delete')->only('destroy');

        $this->adminService = $adminService;
        $this->dashboardService = $dashboardService;
    }

    public function home()
    {
        $dataKeys = [
            'eventFrequencyOverTime',
            'countEventsFrequencyOverTime',
            'mostAccessedURLs',
            'uniqueIPCounts',
            'uniqueIPCount',
            'mostAccessedCourses',
            'peakHours',
            'ipCounts',
            'studentsPerCourse',
            'topStudentsCourses',
            'cityActivity',
            'activityByDayOfWeek',
            'activeUserCount',
            'activeLectureCount',
            'activeCourseCount',
            'browsers',
            'os',
            'lastMembers',
            'heatMap'
        ];

        $data = [];
        foreach ($dataKeys as $key) {
            $methodName = 'get' . ucfirst($key);
            $data[$key] = $this->dashboardService->$methodName();
        }

        return view('admin.home', $data);
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
        $admins = $this->adminService->getAdmins();
        return view('admin.admin.index', compact('admins'));
    }

    // create admin
    public function create()
    {
        $roles = Role::get();
        return view('admin.admin.create', compact('roles'));
    }

    // store admin
    public function store(AdminRequest $request)
    {
        $data = $request->validated();

        $this->adminService->createAdmin($data);

        Toastr::success(__('messages.admin_created'), __('status.success'));

        return redirect()->route('admin.all_admin.index');
    }

    // edit admin
    public function edit($id)
    {
        $roles = Role::get();

        $admin = $this->adminService->getAdmin($id);

        return view('admin.admin.edit', compact('admin', 'roles'));
    }

    // update admin
    public function update(AdminRequest $request, $id)
    {
        $data = $request->validated();

        $all_admin = $this->adminService->getAdmin($id);

        $this->adminService->updateAdmin($data, $all_admin);
        Toastr::success(__('messages.admin_updated'), __('status.success'));

        return redirect()->route('admin.all_admin.index');
    }

    // delete admin
    public function destroy($id)
    {
        $all_admin = $this->adminService->getAdmin($id);
        $this->adminService->deleteAdmin($all_admin) ? Toastr::success(__('messages.admin_deleted'), __('status.success')) : '';

        return redirect()->route('admin.all_admin.index');
    }
}
