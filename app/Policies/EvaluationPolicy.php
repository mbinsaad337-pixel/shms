<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\User;

class EvaluationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-grades');
    }

    public function view(User $user, StudentGrade $grade): bool
    {
        return $user->can('view-grades');
    }

    public function create(User $user, Student $student): bool
    {
        if (! $student->allows('evaluation')) {
            return false;
        }

        return $user->can('manage-grades')
            || $user->hasRole(['center-manager', 'housing-manager', 'supervisor']);
    }

    public function update(User $user, StudentGrade $grade): bool
    {
        if (! $grade->student?->allows('evaluation')) {
            return false;
        }

        return $user->can('manage-grades')
            || $user->hasRole(['center-manager', 'housing-manager']);
    }

    public function delete(User $user, StudentGrade $grade): bool
    {
        return $user->hasRole(['center-manager', 'super-admin']);
    }
}
