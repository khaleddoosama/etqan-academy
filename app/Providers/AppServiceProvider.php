<?php

namespace App\Providers;

use App\Services\AdminService;
use App\Services\AwsS3Service;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\CourseService;
use App\Services\GalleryService;
use App\Services\InquiryService;
use App\Services\InstructorService;
use App\Services\LectureService;
use App\Services\PermissionService;
use App\Services\ProgramService;
use App\Services\ReferralService;
use App\Services\RequestCourseService;
use App\Services\RolePermissionService;
use App\Services\RoleService;
use App\Services\SectionService;
use App\Services\StudentService;
use App\Services\UserCoursesService;
use App\Services\UserService;
use App\Services\WithdrawalRequestService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registering Singleton Services
        $this->registerSingletonServices();
    }

    /**
     * Register Singleton Services
     */
    protected function registerSingletonServices(): void
    {


        $this->app->singleton(AdminService::class, function ($app) {
            return new AdminService($app->make(RoleService::class));
        });

        $this->app->singleton(AwsS3Service::class, function ($app) {
            return new AwsS3Service();
        });

        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService();
        });

        $this->app->singleton(CommentService::class, function ($app) {
            return new CommentService();
        });

        $this->app->singleton(CourseService::class, function ($app) {
            return new CourseService();
        });

        $this->app->singleton(GalleryService::class, function ($app) {
            return new GalleryService();
        });

        $this->app->singleton(InquiryService::class, function ($app) {
            return new InquiryService();
        });

        $this->app->singleton(InstructorService::class, function ($app) {
            return new InstructorService();
        });

        $this->app->singleton(LectureService::class, function ($app) {
            return new LectureService(
                $app->make(UserCoursesService::class),
                $app->make(SectionService::class)
            );
        });

        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });

        $this->app->singleton(ProgramService::class, function ($app) {
            return new ProgramService();
        });

        $this->app->singleton(ReferralService::class, function ($app) {
            return new ReferralService(
                $app->make(UserService::class)
            );
        });

        $this->app->singleton(RequestCourseService::class, function ($app) {
            return new RequestCourseService(
                $app->make(UserCoursesService::class),
                $app->make(UserService::class),
                $app->make(CourseService::class)
            );
        });

        $this->app->singleton(RolePermissionService::class, function ($app) {
            return new RolePermissionService();
        });

        $this->app->singleton(RoleService::class, function ($app) {
            return new RoleService();
        });

        $this->app->singleton(SectionService::class, function ($app) {
            return new SectionService();
        });

        $this->app->singleton(StudentService::class, function ($app) {
            return new StudentService();
        });

        $this->app->singleton(UserCoursesService::class, function ($app) {
            return new UserCoursesService();
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService();
        });

        $this->app->singleton(WithdrawalRequestService::class, function ($app) {
            return new WithdrawalRequestService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
