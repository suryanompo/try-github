<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, ?Project $project = null): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->role === 'project_manager') {
            if ($project) {
                return $project->manager_id === $user->id;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->role === 'project_manager' && $task->project->manager_id === $user->id) {
            return true;
        }

        // Member assigned to this task can update task status
        return $task->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->role === 'project_manager' && $task->project->manager_id === $user->id;
    }
}
