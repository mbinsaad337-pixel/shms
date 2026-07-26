<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Models\Violation;

class ViolationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-violations');
    }

    public function view(User $user, Violation $violation): bool
    {
        return $user->can('view-violations');
    }

    public function create(User $user, Student $student): bool
    {
        if (! $student->allows('violations')) {
            return false;
        }

        return $user->can('manage-violations')
            || $user->hasRole(['center-manager', 'housing-manager', 'supervisor']);
    }

    public function update(User $user, Violation $violation): bool
    {
        return $user->can('manage-violations')
            || $user->hasRole(['center-manager', 'housing-manager']);
    }

    public function delete(User $user, Violation $violation): bool
    {
        return $user->hasRole(['center-manager', 'super-admin']);
    }
}
