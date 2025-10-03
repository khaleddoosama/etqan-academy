<?php

use App\Http\Controllers\Admin\Accounting\EntryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\Accounting\CategoryController as AccountingCategoryController;
use App\Http\Controllers\Admin\Accounting\ReportController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseInstallmentController;
use App\Http\Controllers\Admin\CourseOfferController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LectureController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentDetailController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RequestCourseController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentOpinionController;
use App\Http\Controllers\Admin\StudentWorkController;
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
        Route::prefix('admin')->middleware(['auth', 'role:admin', 'web', 'throttle:60,1', 'log_user_activity:web'])->as('admin.')->group(function () {

            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/notifications/read', [NotificationController::class, 'read'])->name('notifications.read');
            Route::get('/clear-cache', [GeneralController::class, 'clearCache'])->name('clear_cache');
            // admin controller (resource)
            Route::resource('all_admin', AdminController::class)->except(['show']);
            // Admin Controller
            Route::controller(AdminController::class)->group(function () {
                Route::get('/', 'home')->name('home');
                Route::get('/profile', 'profile')->name('profile');
                Route::put('/profile', 'updateProfile')->name('profile.update');

                Route::put('/change-password', 'changePassword')->name('change.password');
                Route::get('/{admin}/logs', 'logs')->name('admins.logs');
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
                Route::get('/users/{user}/logs', 'logs')->name('users.logs');
            });

            // Instructor Controller
            Route::resource('instructors', InstructorController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.instructors.index');
            });

            // Student Works Controller
            Route::resource('student_works', StudentWorkController::class)->except(['show', 'edit', 'update'])->missing(function () {
                return redirect()->route('admin.student_works.index');
            });
            Route::post('/student_works/update-order', [StudentWorkController::class, 'updateOrder'])->name('student_works.updateOrder');

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
            Route::controller(CourseController::class)->group(function () {
                Route::put('/courses/{course}/status', 'status')->name('courses.status');
            });

            // Course Installment Controller
            Route::resource('course_installments', CourseInstallmentController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.course_installments.index');
            });

            // Course Offer Controller
            Route::resource('course_offers', CourseOfferController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.course_offers.index');
            });

            // UserCourse Controller
            Route::controller(UserCoursesController::class)->group(function () {
                Route::get('/users/{user}/courses', 'index')->name('users.courses.index');
                Route::get('courses/{course}/students', 'showStudents')->name('courses.students.index');
                Route::post('/users/{user}/courses', 'storeByUser')->name('users.courses.store');
                Route::post('/courses/{course}/students', 'storeByCourse')->name('courses.users.store');
                Route::put('/users/{user}/courses/{course}', 'changeStatus')->name('users.courses.change_status');
            });



            Route::prefix('sections')->name('sections.')->controller(SectionController::class)->group(function () {
                Route::get('/{section}', 'show')->name('show');
                Route::get('/course/{course}', 'getSections')->name('get');
                Route::post('/duplicate', 'duplicate')->name('duplicate');
                Route::post('/', 'store')->name('store');
                Route::delete('/{section}', 'destroy')->name('destroy');
                Route::post('/reassign-and-sort', [SectionController::class, 'reassignAndSort'])
                    ->name('reassignAndSort');
                Route::put('/{section}', 'update')->name('update');
                Route::post('/bulk-delete', [SectionController::class, 'bulkDelete'])->name('bulkDelete');
            });



            // Lecture Controller
            Route::resource('lectures', LectureController::class)->except(['show', 'create'])->missing(function () {
                return redirect()->route('admin.courses.index');
            });
            Route::prefix('lectures')->name('lectures.')->group(function () {
                Route::post('duplicate', [LectureController::class, 'duplicate'])->name('duplicate');
                Route::post('reassign-and-sort', [LectureController::class, 'reassignAndSort'])->name('reassignAndSort');
                Route::post('bulk-delete', [LectureController::class, 'bulkDelete'])->name('bulkDelete');
                Route::post('update-order', [LectureController::class, 'updateOrder'])->name('updateOrder');

                Route::put('is-free/{lecture}', [LectureController::class, 'changeIsFree'])->name('changeIsFree');
                Route::put('update-attachment/{lecture}', [LectureController::class, 'updateAttachment'])->name('updateAttachment');
                Route::put('delete-attachment/{lecture}', [LectureController::class, 'deleteAttachment'])->name('deleteAttachment');

                Route::get('{section_id}/get', [LectureController::class, 'getLectures'])->name('get');

                // Uncomment as needed:
                // Route::get('failed', [LectureController::class, 'failedLectures'])->name('failed.index');
                // Route::post('upload-video', [LectureController::class, 'generatePresignedUrl']);
                // Route::put('update-video-path/{lecture}', [LectureController::class, 'updateVideoPath'])->name('updateVideoPath');
            });

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

            // Payment Detail Controller
            Route::controller(PaymentDetailController::class)->group(function () {
                Route::get('/payment-details', 'index')->name('payment_details.index');
                Route::get('payment-details/data',  'data')->name('payment_details.data');

                Route::get('/payment-details/export', 'export')->name('payment_details.export');
                Route::get('/payment-details/download', 'download')->name('payment_details.download');
                Route::get('/payment-details/{id}', 'show')->name('payment_details.show');
                Route::put('/payment-details/{id}/status', 'status')->name('payment_details.status');
                Route::put('/payment-details/{id}/update-amount', 'updateAmountConfirmed')->name('payment_details.update_amount');
                Route::put('/payment-details/{id}/update-coupon', 'updateCoupon')->name('payment_details.update_coupon');
                Route::put('/payment-details/{id}/update-paid-at', 'updatePaidAt')->name('payment_details.update_paid_at');
            });

            // Permission controller (resource)
            Route::resource('permission', PermissionController::class)->except(['show']);

            // Role controller (resource)
            Route::resource('role', RoleController::class)->except(['show']);

            // role permission controller (resource) with prefix role-permission and as role_permission.
            Route::resource('role_permissions', RolePermissionController::class)->only(['index', 'edit', 'update']);

            // logs
            Route::controller(LogController::class)->group(function () {
                Route::get('/logs/files', 'allFiles')->name('logs.files.index');
                Route::get('/logs/files/{file}', 'showFile')->name('logs.files.show');
                Route::get('/logs/files/{file}/download', 'download')->name('logs.files.download');
                Route::delete('/logs/files/{file}', 'delete')->name('logs.files.delete');

                Route::get('/logs', 'index')->name('logs.index');
                Route::get('/logs/{log}', 'show')->name('logs.show');
                Route::delete('/logs/bulk-delete/{type}', 'bulkDelete')->name('logs.bulk_delete');

                Route::get('/databases', 'allDatabases')->name('databases.index');
                Route::get('/databases/{database}/download', 'downloadDatabase')->name('databases.download');
                Route::delete('/databases/{database}', 'deleteDatabase')->name('databases.delete');
            });

            // jobs
            Route::controller(JobController::class)->group(function () {
                Route::get('/jobs', 'index')->name('jobs.index');

                Route::get('/failed_jobs', 'failedJobs')->name('failed_jobs.index');
                Route::post('/failed_jobs/retry/{id}', 'retry')->name('failed_jobs.retry');
                Route::delete('/failed_jobs/delete/{id}', 'delete')->name('failed_jobs.delete');
                Route::post('/failed_jobs/retry_all', 'retryAll')->name('failed_jobs.retry_all');
                Route::delete('/failed_jobs/delete_all', 'deleteAll')->name('failed_jobs.delete_all');
            });

            // student opinions
            Route::controller(StudentOpinionController::class)->group(function () {
                Route::get('/student-opinions', 'index')->name('student-opinions.index');
                Route::put('/student-opinions/{id}/status', 'status')->name('student-opinions.status');
            });

            // coupons
            Route::resource('coupons', CouponController::class)->except(['show']);
            Route::put('/coupons/{id}/status', [CouponController::class, 'status'])->name('coupons.status');

            Route::resource('packages', PackageController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.packages.index');
            });

            // accounting
            Route::prefix('accounting')->name('accounting.')->group(function () {
                // accounting category
                Route::resource('categories', AccountingCategoryController::class)->except(['show'])->missing(function () {
                    return redirect()->route('admin.accounting.categories.index');
                });

                Route::controller(EntryController::class)->group(function () {
                    Route::get('/entries', 'index')->name('entries.index');
                    Route::get('entries/data',  'data')->name('entries.data');
                    Route::get('entries/statistics',  'statistics')->name('entries.statistics');
                    Route::get('/entries/create', 'create')->name('entries.create');
                    Route::post('/entries', 'store')->name('entries.store');
                    Route::get('/entries/{entry}/edit', 'edit')->name('entries.edit');
                    Route::put('/entries/{entry}', 'update')->name('entries.update');
                    Route::delete('/entries/{entry}', 'destroy')->name('entries.destroy');
                });

                // Reports
                Route::controller(ReportController::class)->group(function () {
                    Route::get('/reports', 'index')->name('reports.index');
                    Route::get('/reports/data', 'data')->name('reports.data');
                    Route::get('/reports/charts', 'charts')->name('reports.charts');
                    Route::get('/reports/statistics', 'statistics')->name('reports.statistics');
                    Route::get('/reports/export', 'export')->name('reports.export');
                });
            });
        });
    }
);
