<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterBySupervisor
{
    protected static function bootFilterBySupervisor()
    {
        static::addGlobalScope('supervisor_filter', function (Builder $query) {
            if (auth()->check()) {
                $user = auth()->user();
                // Check if user is exactly one of the supervisors and not a higher admin
                if (!$user->hasRole('super-admin') && !$user->hasRole('center-manager') && !$user->hasRole('executive-manager') && !$user->hasRole('student-supervisor')) {
                    if ($user->hasRole('academic-supervisor')) {
                        $query->whereHas('student', function ($q) {
                            $q->withoutGlobalScope('supervisor')->whereHas('program', fn ($p) => $p->where('code', 'academic'));
                        });
                    } elseif ($user->hasRole('cooperative-supervisor')) {
                        $query->whereHas('student', function ($q) {
                            $q->withoutGlobalScope('supervisor')->whereHas('program', fn ($p) => $p->where('code', 'cooperative'));
                        });
                    }
                }
            }
        });
    }
}
