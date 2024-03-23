<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LectureController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;



//----------------------------- Admin Routes -----------------------------//
    


Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::prefix('admin')->middleware(['auth', 'role:admin', 'web'])->as('admin.')->group(function () {
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
                Route::get('/users/pending', 'pending')->name('users.pending');
                Route::get('/users/active', 'active')->name('users.active');
                Route::get('/users/inactive', 'inactive')->name('users.inactive');

                Route::get('/users/{user}/edit', 'edit')->name('users.edit');
                Route::put('/users/{user}', 'update')->name('users.update');
                Route::put('/users/{user}/status', 'status')->name('users.status');

                Route::put('/{user}/password', 'updatePassword')->name('users.update.password');
            });

            // Category Controller
            Route::resource('categories', CategoryController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.categories.index');
            });

            // Course Controller
            Route::resource('courses', CourseController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.courses.index');
            });

            Route::controller(SectionController::class)->group(function () {
                // show section
                Route::get('/sections/{section}', 'show')->name('sections.show');
            });

            // Lecture Controller
            Route::resource('lectures', LectureController::class)->except(['show'])->missing(function () {
                return redirect()->route('admin.lectures.index');
            });




            // Permission controller (resource)
            // Route::resource('permission', RolePermissionController::class)->except(['show']);

            // Role controller (resource)
            Route::resource('role', RoleController::class)->except(['show']);

            // role permission controller (resource) with prefix role-permission and as role_permission.
            // Route::resource('role_permission', RolePermissionController::class)->except(['show', 'destroy']);

            // support
            Route::get('/support', [SupportController::class, 'index'])->name('support.index');
        });
    }
);
