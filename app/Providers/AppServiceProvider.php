<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Violation;
use App\Models\Activity;
use App\Models\StudentGrade;
use App\Policies\ViolationPolicy;
use App\Policies\ActivityPolicy;
use App\Policies\EvaluationPolicy;
use App\Policies\AttendancePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // On production (InfinityFree), the root folder is served as public
        // This does NOT apply locally so Vite can find its manifest in public/build/
        if ($this->app->environment('production')) {
            $this->app->usePublicPath(base_path());
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super Admin bypasses all gates
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Program-aware Policies
        Gate::policy(Violation::class,    ViolationPolicy::class);
        Gate::policy(Activity::class,     ActivityPolicy::class);
        Gate::policy(StudentGrade::class, EvaluationPolicy::class);

        // Attendance (no dedicated model, uses Gate define)
        Gate::define('record-attendance', function ($user, $student) {
            return (new AttendancePolicy)->create($user, $student);
        });
    }
}
