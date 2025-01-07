<?php

namespace App\Providers;

use App\Events\CourseRequestEvent;
use App\Events\CourseRevokeSoonEvent;
use App\Events\CreatePaymentDetailEvent;
use App\Events\CreateWithdrawalRequestEvent;
use App\Events\NewCourseEvent;
use App\Events\PaymentApprovedEvent;
use App\Events\PaymentRejectedEvent;
use App\Events\RegisterUserEvent;
use App\Events\SentInquiryEvent;
use App\Events\VerifyMailEvent;
use App\Listeners\CourseRequestListener;
use App\Listeners\CourseRevokeSoonListener;
use App\Listeners\CreatePaymentDetailListener;
use App\Listeners\CreateWithdrawalRequestListener;
use App\Listeners\NewCourseListener;
use App\Listeners\PaymentApprovedListener;
use App\Listeners\PaymentRejectedListener;
use App\Listeners\RegisterUserListener;
use App\Listeners\SentInquiryListener;
use App\Listeners\VerifyMailListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        CourseRevokeSoonEvent::class => [
            CourseRevokeSoonListener::class,
        ],

        CourseRequestEvent::class => [
            CourseRequestListener::class,
        ],

        VerifyMailEvent::class => [
            VerifyMailListener::class,
        ],

        SentInquiryEvent::class => [
            SentInquiryListener::class
        ],

        NewCourseEvent::class => [
            NewCourseListener::class
        ],

        PaymentApprovedEvent::class => [
            PaymentApprovedListener::class
        ],

        PaymentRejectedEvent::class => [
            PaymentRejectedListener::class
        ],

        CreatePaymentDetailEvent::class => [
            CreatePaymentDetailListener::class
        ],

        RegisterUserEvent::class => [
            RegisterUserListener::class
        ],

        CreateWithdrawalRequestEvent::class => [
            CreateWithdrawalRequestListener::class
        ],

        
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
