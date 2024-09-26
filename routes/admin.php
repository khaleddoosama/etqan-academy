<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\LectureController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RequestCourseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserCoursesController;
use App\Http\Controllers\Admin\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;



//----------------------------- Admin Routes -----------------------------//



Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::prefix('admin')->middleware(['auth', 'role:admin', 'web', 'throttle:60,1'])->as('admin.')->group(function () {

            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/read', [NotificationController::class, 'read'])->name('notifications.read');

            // admin controller (resource)
            Route::resource('all_admin', AdminController::class)->except(['show']);
            // Admin Controller
            Route::controller(AdminController::class)->group(function () {
                Route::get('/', 'home')->name('home');
                Route::get('/profile', 'profile')->name('profile');
                Route::put('/profile', 'updateProfile')->name('profile.update');

                Route::put('/change-password', 'changePassword')->name('change.password');
            });

            // User Controller
            Route::controller(UserController::class)->group(function () {
                // Route::get('/users/pending', 'pending')->name('users.pending');
                Route::get('/users/active', 'active')->name('users.active');
                Route::get('/users/inactive', 'inactive')->name('users.inactive');

                Route::get('/users/create', 'create')->name('users.create');
                Route::post('/users', 'store')->name('users.store');

                Route::get('/users/{user}/show', 'show')->name('users.show');
                Route::get('/users/{user}/edit', 'edit')->name('users.edit');
                Route::put('/users/{user}/verify', 'verify')->name('users.verify');
                Route::put('/users/{user}', 'update')->name('users.update');
                Route::put('/users/{user}/status', 'status')->name('users.status');

                Route::put('/{user}/password', 'updatePassword')->name('users.update.password');
            });

            // Instructor Controller
            Route::resource('instructors', InstructorController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.instructors.index');
            });

            // Category Controller
            Route::resource('categories', CategoryController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.categories.index');
            });

            // Program Controller
            Route::resource('programs', ProgramController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.programs.index');
            });

            // Course Controller
            Route::resource('courses', CourseController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.courses.index');
            });

            // UserCourse Controller
            Route::controller(UserCoursesController::class)->group(function () {
                Route::get('/users/{user}/courses', 'index')->name('users.courses.index');
                Route::get('courses/{course}/students', 'showStudents')->name('courses.students.index');
                Route::post('/users/{user}/courses', 'store')->name('users.courses.store');
                Route::post('/courses/{course}/students', 'store2')->name('courses.users.store');
                Route::put('/users/{user}/courses/{course}', 'changeStatus')->name('users.courses.change_status');
            });


            Route::controller(SectionController::class)->group(function () {
                // show section
                Route::get('/sections/{section}', 'show')->name('sections.show');
            });

            // Lecture Controller
            Route::resource('lectures', LectureController::class)->except(['show', 'create', 'index'])->missing(function () {
                return redirect()->route('admin.courses.index');
            });
            Route::post('lectures/duplicate', [LectureController::class, 'duplicate'])->name('lectures.duplicate');
            Route::post('/lectures/update-order', [LectureController::class, 'updateOrder'])->name('lectures.updateOrder');
            Route::post('/upload-video', [LectureController::class, 'generatePresignedUrl']);
            Route::put('/update-attachment/{lecture}', [LectureController::class, 'updateAttachment'])->name('lectures.updateAttachment');
            Route::put('/delete-attachment/{lecture}', [LectureController::class, 'deleteAttachment'])->name('lectures.deleteAttachment');
            Route::put('lectures/is-free/{lecture}', [LectureController::class, 'changeIsFree'])->name('lectures.changeIsFree');

            // Inquiry Controller
            Route::controller(InquiryController::class)->group(function () {
                Route::get('/inquiries', 'index')->name('inquiries.index');
                Route::get('/inquiries/{id}', 'show')->name('inquiries.show');
                Route::put('/inquiries/{id}/reply', 'reply')->name('inquiries.reply');
            });

            // Withdrawal Request Controller
            Route::controller(WithdrawalRequestController::class)->group(function () {
                Route::get('/withdrawal-requests', 'index')->name('withdrawal_requests.index');
                Route::get('/withdrawal-requests/{withdrawalRequest}', 'show')->name('withdrawal_requests.show');
                Route::put('/withdrawal-requests/{withdrawalRequest}/status', 'status')->name('withdrawal_requests.status');
            });

            // Request Course Controller
            Route::controller(RequestCourseController::class)->group(function () {
                Route::get('/request-courses', 'index')->name('request_courses.index');
                Route::get('/request-courses/{id}', 'show')->name('request_courses.show');
                Route::put('/request-courses/{id}/status', 'status')->name('request_courses.status');
            });

            // Permission controller (resource)
            Route::resource('permission', PermissionController::class)->except(['show']);

            // Role controller (resource)
            Route::resource('role', RoleController::class)->except(['show']);

            // role permission controller (resource) with prefix role-permission and as role_permission.
            Route::resource('role_permissions', RolePermissionController::class)->only(['index', 'edit', 'update']);
        });
    }
);
 