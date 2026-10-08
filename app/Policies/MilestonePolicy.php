<?php

namespace App\Policies;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\User;

class MilestonePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Milestone $milestone): bool
    {
        return true;
    }

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

    public function update(User $user, Milestone $milestone): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->role === 'project_manager' && $milestone->project->manager_id === $user->id;
    }

    public function delete(User $user, Milestone $milestone): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->role === 'project_manager' && $milestone->project->manager_id === $user->id;
    }
}
