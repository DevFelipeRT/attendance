<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ClassGroup\AttendanceService;
use App\Services\ClassGroup\ClassEnrollmentService;
use App\Services\ClassGroup\ClassGroupService;
use App\Services\ClassGroup\ClassLessonService;
use App\Services\Mentorship\MentorshipAttendanceService;
use App\Services\Mentorship\MentorshipBillingService;
use App\Services\Mentorship\MentorshipService;
use App\Services\Mentorship\MentorshipSessionService;
use Illuminate\Support\ServiceProvider;

/**
 * Application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Class group services
        $this->app->singleton(ClassGroupService::class);
        $this->app->singleton(ClassEnrollmentService::class);
        $this->app->singleton(ClassLessonService::class);
        $this->app->singleton(AttendanceService::class);

        // Mentorship services
        $this->app->singleton(MentorshipService::class);
        $this->app->singleton(MentorshipSessionService::class);
        $this->app->singleton(MentorshipAttendanceService::class);
        $this->app->singleton(MentorshipBillingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
