<?php

namespace App\Http\Controllers\Admin;

use App\Events\VerifyMailEvent;
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
        $this->middleware('permission:user.show')->only('show', 'logs');
        $this->middleware('permission:user.edit')->only('edit', 'update', 'updatePassword');
        $this->middleware('permission:user.status')->only('status');
    }

    //active
    public function active(Request $request)
    {
        // Get per page value from request or default to 25
        $perPage = $request->get('per_page', 25);

        // Validate per_page value to prevent abuse
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        // Get search parameter
        $search = $request->get('search');

        $users = $this->userService->getActiveUsers($perPage, $search);

        // Preserve query parameters in pagination
        $users->appends($request->query());

        $title = __('attributes.users_active');

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.user.users-table', compact('users'))->render();
        }

        return view('admin.user.index', compact('users', 'title'));
    }

    //inactive
    public function inactive(Request $request)
    {
        // Get per page value from request or default to 25
        $perPage = $request->get('per_page', 25);

        // Validate per_page value to prevent abuse
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        // Get search parameter
        $search = $request->get('search');

        $users = $this->userService->getInactiveUsers($perPage, $search);

        // Preserve query parameters in pagination
        $users->appends($request->query());

        $title = __('attributes.users_inactive');

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.user.users-table', compact('users'))->render();
        }

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

        event(new VerifyMailEvent([$user->id]));

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

    // verify
    public function verify($id)
    {
        $user = $this->userService->getUser($id);
        $user->update(['email_verified_at' => now()]);
        return redirect()->back();
    }    // logs
    public function logs($id, Request $request)
    {
        $user = $this->userService->getUser($id);

        // Start building the query
        $query = \Spatie\Activitylog\Models\Activity::query();

        // Base filter: either user is causer or subject (unless filtered by causer_type)
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
                // Only logs where the user performed the action
                $query->where('causer_id', $id)
                      ->where('causer_type', 'App\Models\User');
            } elseif ($request->causer_type === 'performed_on_user') {
                // Only logs where actions were performed on the user
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

        $logNames = $logNamesQuery->distinct()->pluck('log_name');

        // Check if this is an AJAX request
        if ($request->ajax()) {
            return view('admin.user.logs-table', compact('logs', 'user'))->render();
        }

        return view('admin.user.logs', compact('user', 'logs', 'logNames'));
    }

}
