<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class AttendancePolicy
{
    /**
     * هل يمكن تسجيل الغياب على هذا الطالب؟
     */
    public function create(User $user, Student $student): bool
    {
        if (! $student->allows('attendance')) {
            return false;
        }

        return $user->can('manage-absences')
            || $user->hasRole(['center-manager', 'housing-manager', 'supervisor']);
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view-absences');
    }
}
