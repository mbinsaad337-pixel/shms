<?php

namespace App\Http\Controllers;

use App\Models\CircleAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CircleAbsenceController extends Controller
{
    public function destroy(CircleAttendance $attendance)
    {
        $user = Auth::user();

        // Ensure user is authorized to manage center
        // Or specific roles: housing-manager, center-manager, etc.
        if (!$user->can('manage-quran-circles') && !$user->can('view-circle-reports')) {
            abort(403);
        }

        // Verify it's an absence record
        if ($attendance->status === 'absent') {
            $attendance->delete();
            return back()->with('success', 'تم حذف سجل الغياب بنجاح (معذور).');
        }

        return back()->with('error', 'لا يمكن حذف هذا السجل.');
    }
}
