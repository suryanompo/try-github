<?php

namespace App\Providers;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Policies\MilestonePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Milestone::class, MilestonePolicy::class);

        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('manage-projects', fn (User $user) => $user->isAdmin() || $user->role === 'project_manager');
        Gate::define('view-reports', fn (User $user) => $user->isAdmin() || $user->role === 'project_manager');
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
    }
}
