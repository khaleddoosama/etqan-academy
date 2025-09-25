<?php

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentType;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Payment;
use App\Models\PaymentItems;
use App\Models\User;
use App\Models\UserCourse;
use App\Services\PaymentDetailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Carbon::setTestNow('2025-01-01 10:00:00');
});

it('grants limited-time access when using installment coupon', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $coupon = Coupon::factory()->create([
        'code' => 'installment',
        'access_duration_days' => 7,
    ]);

    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'coupon_id' => $coupon->id,
        'discount' => $coupon->discount,
        'type' => $coupon->type,
        'status' => PaymentStatusEnum::Pending,
        'gateway' => 'instapay',
        'amount_before_coupon' => 1000,
        'amount_after_coupon' => 900,
        'amount_confirmed' => 900,
    ]);

    PaymentItems::factory()->create([
        'payment_id' => $payment->id,
        'course_id' => $course->id,
        'payment_type' => PaymentType::CASH,
        'amount' => 900,
    ]);

    app(PaymentDetailService::class)->changeStatus(PaymentStatusEnum::Paid->value, $payment->id);

    $expectedExpiry = Carbon::now()->addDays(7)->toDateTimeString();

    $record = UserCourse::where('student_id', $user->id)->where('course_id', $course->id)->first();
    expect($record)->not()->toBeNull();
    expect($record->status)->toBe(1);
    expect($record->expires_at?->toDateTimeString())->toBe($expectedExpiry);
});

it('makes access unlimited when using any non-installment coupon', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $couponLimited = Coupon::factory()->create([
        'code' => 'installment',
        'access_duration_days' => 7,
    ]);

    $payment1 = Payment::factory()->create([
        'user_id' => $user->id,
        'coupon_id' => $couponLimited->id,
        'discount' => $couponLimited->discount,
        'type' => $couponLimited->type,
        'status' => PaymentStatusEnum::Pending,
        'gateway' => 'instapay',
        'amount_before_coupon' => 1000,
        'amount_after_coupon' => 900,
        'amount_confirmed' => 900,
    ]);
    PaymentItems::factory()->create([
        'payment_id' => $payment1->id,
        'course_id' => $course->id,
        'payment_type' => PaymentType::CASH,
        'amount' => 900,
    ]);
    app(PaymentDetailService::class)->changeStatus(PaymentStatusEnum::Paid->value, $payment1->id);

    $couponUnlimited = Coupon::factory()->create([
        'code' => 'welcome10',
        'access_duration_days' => null,
    ]);
    $payment2 = Payment::factory()->create([
        'user_id' => $user->id,
        'coupon_id' => $couponUnlimited->id,
        'discount' => $couponUnlimited->discount,
        'type' => $couponUnlimited->type,
        'status' => PaymentStatusEnum::Pending,
        'gateway' => 'instapay',
        'amount_before_coupon' => 1000,
        'amount_after_coupon' => 900,
        'amount_confirmed' => 900,
    ]);
    PaymentItems::factory()->create([
        'payment_id' => $payment2->id,
        'course_id' => $course->id,
        'payment_type' => PaymentType::CASH,
        'amount' => 900,
    ]);
    app(PaymentDetailService::class)->changeStatus(PaymentStatusEnum::Paid->value, $payment2->id);

    $record = UserCourse::where('student_id', $user->id)->where('course_id', $course->id)->first();
    expect($record)->not()->toBeNull();
    expect($record->status)->toBe(1);
    expect($record->expires_at)->toBeNull();
});

it('revokes access after limited period expires via scheduled command', function () {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $uc = UserCourse::create([
        'student_id' => $user->id,
        'course_id' => $course->id,
        'status' => 1,
        'expires_at' => Carbon::now()->subDay(),
    ]);

    Artisan::call('app:revoke-expired-access');

    $uc->refresh();
    expect($uc->status)->toBe(0);
});

