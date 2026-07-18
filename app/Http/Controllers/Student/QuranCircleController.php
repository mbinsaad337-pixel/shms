<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\QuranCircle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuranCircleController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'يجب أن تكون طالباً مسجلاً لتتمكن من الوصول لهذا القسم');
        }

        $circles = $student->quranCircles()->with('teacher')->get();

        foreach ($circles as $circle) {
            $circle->attendanceCount = \App\Models\CircleAttendance::where('student_id', $student->id)
                ->whereHas('session', function ($q) use ($circle) {
                    $q->where('circle_id', $circle->id);
                })
                ->where('status', 'present')
                ->count();

            $circle->absenceCount = \App\Models\CircleAttendance::where('student_id', $student->id)
                ->whereHas('session', function ($q) use ($circle) {
                    $q->where('circle_id', $circle->id);
                })
                ->where('status', 'absent')
                ->count();
        }

        return view('student.quran-circles.index', compact('circles'));
    }
}
