<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use App\Models\Activity;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view-activities');
    }

    public function create(User $user): bool
    {
        return $user->can('manage-activities')
            || $user->hasRole(['center-manager', 'social-manager']);
    }

    /**
     * هل يمكن تسجيل الطالب في نشاط؟
     */
    public function participate(User $user, Student $student): bool
    {
        return $student->allows('activities');
    }

    /**
     * هل يمكن تسجيل حضور الطالب في نشاط؟
     */
    public function recordAttendance(User $user, Student $student): bool
    {
        return $student->allows('activities')
            && ($user->can('manage-activities') || $user->hasRole(['activity-assistant', 'social-manager']));
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->can('manage-activities')
            || $user->hasRole(['center-manager', 'social-manager']);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->hasRole(['center-manager', 'super-admin']);
    }
}
