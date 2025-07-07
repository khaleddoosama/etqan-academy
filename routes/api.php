<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\InquiryController;
use App\Http\Controllers\Api\LectureController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PaymentDetailController;
use App\Http\Controllers\Api\PaymentGateway\GatewayPaymentController;
use App\Http\Controllers\Api\PaymentGateway\GatewayWebhookController;
use App\Http\Controllers\Api\RequestCourseController;
use App\Http\Controllers\Api\ResetPasswordController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentOpinionController;
use App\Http\Controllers\Api\StudentWorkController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\WithdrawalRequestController;
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

// Public routes with user activity logging
Route::middleware(['log_user_activity:api'])->group(function () {

    // Authentication routes
    Route::group([
        'middleware' => ['throttle:60,1'],
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

    // Public content routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/student-works', [StudentWorkController::class, 'index']);
    Route::get('/home', [HomeController::class, 'home']);
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/course/{course_slug}', [CourseController::class, 'show']);
    Route::get('/course/{course_slug}/sections', [SectionController::class, 'index']);
    Route::get('course/{course_slug}/section/{section_slug}', [SectionController::class, 'show']);
    Route::get('course/{course_slug}/section/{section_slug}/lectures', [LectureController::class, 'index']);
    Route::get('/student-opinions', [StudentOpinionController::class, 'index']);

    // Package routes
    Route::prefix('/packages')->group(function () {
        Route::get('/', [PackageController::class, 'index']);
        Route::get('/{slug}', [PackageController::class, 'show']);
    });

    // Public form submissions with throttling
    Route::middleware(['throttle:20,1'])->group(function () {
        Route::post('/send-inquiry', [InquiryController::class, 'sendInquiry']);
        Route::post('/request-course', [RequestCourseController::class, 'store']);
        Route::post('password/request', [ResetPasswordController::class, 'requestPassword'])->name('password.request');
        Route::post('password/reset', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');
    });

    // Email verification (signed route)
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
        ->name('jwt.verification.verify')->middleware(['signed', 'throttle:20,1']);
});

// Authenticated routes with user activity logging
Route::middleware(['jwt.authenticate', 'jwt.verified', 'throttle:20,1', 'log_user_activity:api'])->group(function () {

    Route::get('/user-courses', [UserController::class, 'UserCourses']);

    // Course content access (protected)
    Route::prefix('course/{course_slug}/section/{section_slug}/lecture/{lecture_slug}')->group(function () {
        Route::get('/', [LectureController::class, 'show']);
        Route::put('/view', [LectureController::class, 'views']);
    });

    // Student features
    Route::prefix('students')->group(function () {
        Route::get('/search', [StudentController::class, 'search']);
        Route::get('/{slug}', [StudentController::class, 'showProfile']);
    });

    // User content management
    Route::prefix('gallery')->group(function () {
        Route::post('/', [GalleryController::class, 'store']);
        Route::delete('/{id}', [GalleryController::class, 'destroy']);
    });

    // User interactions
    Route::post('/payment-details', [PaymentDetailController::class, 'store']);
    Route::post('withdrawal-request', [WithdrawalRequestController::class, 'store']);
    Route::post('/student-opinions', [StudentOpinionController::class, 'store']);
    Route::apiResource('comments', CommentController::class);



    // Coupons and payments
    Route::get('/coupon-apply', [CouponController::class, 'applyCoupon'])->middleware('throttle:20,1');
    Route::prefix('payment/fawaterak')->middleware(['throttle:20,1'])->group(function () {
        Route::get('/payment-methods', [GatewayPaymentController::class, 'paymentMethods']);
        Route::post('/pay', [GatewayPaymentController::class, 'pay']);
    });

    // Instapay payment
    Route::post('/payment/instapay', [GatewayPaymentController::class, 'payInstapay'])->middleware('throttle:20,1');
});

// Authenticated routes for email verification (with user activity logging)
Route::middleware(['jwt.authenticate', 'throttle:20,1', 'log_user_activity:api'])->group(function () {
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationEmail'])
        ->name('verification.send');

    // Shopping cart
    Route::prefix('carts')->group(function () {
        Route::get('/', [CartController::class, 'getForUser']);
        Route::post('/', [CartController::class, 'store'])->middleware('throttle:20,1');
        Route::delete('/{cartId}', [CartController::class, 'destroy']);
    });
});

// Webhook routes (no user activity logging needed)
Route::prefix('/payment/fawaterak')->group(function () {
    Route::post('/webhook/paid_json', [GatewayWebhookController::class, 'handlePaid']);
    Route::post('/webhook/cancelled_json', [GatewayWebhookController::class, 'handleCancelled']);
    Route::post('/webhook/failed_json', [GatewayWebhookController::class, 'handleFailed']);
    Route::post('/webhook/refund_json', [GatewayWebhookController::class, 'handleRefund']);
});
