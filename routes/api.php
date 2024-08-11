<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\LectureController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\WithdrawalRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/update-picture', [AuthController::class, 'updatePicture']);
});

// send Inquiry
Route::post('/send-inquiry', [InquiryController::class, 'sendInquiry']);
Route::get('/categories', [CategoryController::class, 'index']);

// show courses
Route::get('/courses', [CourseController::class, 'index']);

// show single course
Route::get('/course/{course_slug}', [CourseController::class, 'show']);
// show sections
Route::get('/course/{course_slug}/sections', [SectionController::class, 'index']);

// show single section
Route::get('course/{course_slug}/section/{section_slug}', [SectionController::class, 'show']);

// show lectures
Route::get('course/{course_slug}/section/{section_slug}/lectures', [LectureController::class, 'index']);

Route::middleware(['jwt.auth', 'verified'])->group(function () {
    // show single lecture
    Route::get('course/{course_slug}/section/{section_slug}/lecture/{lecture_slug}', [LectureController::class, 'show']);

    // Withdrawal Request
    Route::post('withdrawal-request', [WithdrawalRequestController::class, 'store']);
});


Route::middleware(['jwt.auth'])->group(function () {
    // Send the email verification link
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->name('verification.send');

});

// Handle email verification
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
    ->name('jwt.verification.verify')->middleware(['signed', 'throttle:6,1']);
