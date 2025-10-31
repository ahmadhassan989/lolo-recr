<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user) {
            return $user->role === 'super_admin' ? true : null;
        });

        $abilities = [
            'projects.view' => ['super_admin', 'hr'],
            'projects.manage' => ['super_admin'],
            'candidates.view' => ['super_admin', 'hr', 'recruiter'],
            'candidates.manage' => ['super_admin', 'hr'],
            'applications.view' => ['super_admin', 'hr', 'recruiter'],
            'applications.manage' => ['super_admin', 'hr'],
            'applications.update' => ['super_admin', 'hr', 'recruiter'],
            'analytics.view' => ['super_admin'],
            'offers.view' => ['super_admin', 'hr', 'recruiter'],
            'offers.manage' => ['super_admin', 'hr', 'recruiter'],
            'jobs.view' => ['super_admin', 'hr', 'recruiter'],
            'jobs.manage' => ['super_admin', 'hr'],
        ];

        foreach ($abilities as $ability => $roles) {
            Gate::define($ability, fn (User $user) => in_array($user->role, $roles, true));
        }
    }
}
