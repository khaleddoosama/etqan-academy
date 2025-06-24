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

    // logs
    public function logs($id, \Illuminate\Http\Request $request)
    {
        $admin = $this->adminService->getAdmin($id);

        // Start building the query
        $query = \Spatie\Activitylog\Models\Activity::query();

        // Base filter: either admin is causer or subject (unless filtered by causer_type)
        if (!$request->filled('causer_type') || $request->causer_type === 'all') {
            $query->where(function($q) use ($id) {
                $q->where(function($subQ) use ($id) {
                    $subQ->where('causer_id', $id)
                         ->where('causer_type', 'App\Models\User');
                })->orWhere(function($subQ) use ($id) {
                    $subQ->where('subject_id', $id)
                         ->where('subject_type', 'App\Models\User');
                });
            });
        }

        // Apply filters
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by causer/performer type
        if ($request->filled('causer_type')) {
            if ($request->causer_type === 'performed_by_user') {
                // Only logs where the admin performed the action
                $query->where('causer_id', $id)
                      ->where('causer_type', 'App\Models\User');
            } elseif ($request->causer_type === 'performed_on_user') {
                // Only logs where actions were performed on the admin
                $query->where('subject_id', $id)
                      ->where('subject_type', 'App\Models\User');
            }
            // If 'all' is selected, no additional filter is applied (default behavior)
        }

        // Get per page value from request or default to 25
        $perPage = $request->get('per_page', 25);

        // Validate per_page value to prevent abuse
        $allowedPerPage = [10, 25, 50, 100, 500];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        // Get paginated results
        $logs = $query->with(['causer', 'subject']) // Eager load relationships to avoid N+1 queries
            ->orderBy('created_at', 'desc')
            ->paginate($perPage); // Dynamic logs per page

        // Preserve query parameters in pagination
        $logs->appends($request->query());

        // Get unique log names for filter dropdown (using same base query logic)
        $logNamesQuery = \Spatie\Activitylog\Models\Activity::query();

        // Apply same base filter as main query
        if (!$request->filled('causer_type') || $request->causer_type === 'all') {
            $logNamesQuery->where(function($q) use ($id) {
                $q->where(function($subQ) use ($id) {
                    $subQ->where('causer_id', $id)
                         ->where('causer_type', 'App\Models\User');
                })->orWhere(function($subQ) use ($id) {
                    $subQ->where('subject_id', $id)
                         ->where('subject_type', 'App\Models\User');
                });
            });
        }

        // Apply causer_type filter to log names query as well
        if ($request->filled('causer_type')) {
            if ($request->causer_type === 'performed_by_user') {
                $logNamesQuery->where('causer_id', $id)
                              ->where('causer_type', 'App\Models\User');
            } elseif ($request->causer_type === 'performed_on_user') {
                $logNamesQuery->where('subject_id', $id)
                              ->where('subject_type', 'App\Models\User');
            }
        }

        $logTypes = $logNamesQuery->distinct()->pluck('log_name');

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.admin.logs-table', compact('logs', 'admin'))->render();
        }

        return view('admin.admin.logs', compact('admin', 'logs', 'logTypes'));
    }
}
