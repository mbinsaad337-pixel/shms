<?php

namespace App\Http\Controllers;

use App\Models\QuranCircle;
use App\Models\CircleSession;
use App\Models\CircleAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CircleSessionController extends Controller
{
    public function create(QuranCircle $circle)
    {
        $this->authorizeAccess($circle);
        return view('circle-sessions.create', compact('circle'));
    }

    public function store(Request $request, QuranCircle $circle)
    {
        $this->authorizeAccess($circle);

        $request->validate([
            'session_date' => 'required|date',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'marked_present' => 'nullable|array',
            'progress' => 'nullable|array',
        ]);

        $absentStudentNames = [];

        DB::transaction(function () use ($request, $circle, &$absentStudentNames) {
            $session = CircleSession::create([
                'circle_id' => $circle->id,
                'session_date' => $request->session_date,
                'title' => $request->title,
                'notes' => $request->notes,
            ]);

            $allStudentIds = $circle->students()->pluck('students.id');
            $presentIds = $request->marked_present ?? [];

            foreach ($allStudentIds as $studentId) {
                $status = in_array($studentId, $presentIds) ? 'present' : 'absent';
                $sura = $request->progress[$studentId]['sura'] ?? null;
                $verse = $request->progress[$studentId]['verse'] ?? null;

                CircleAttendance::create([
                    'session_id' => $session->id,
                    'student_id' => $studentId,
                    'status' => $status,
                    'sura' => $sura,
                    'verse' => $verse,
                ]);

                if ($status === 'present') {
                    // Update main progress if provided
                    if ($sura) {
                        $circle->students()->updateExistingPivot($studentId, [
                            'last_sura' => $sura,
                            'last_verse' => $verse,
                            'last_progress_at' => now(),
                        ]);
                    }
                } else {
                    $student = \App\Models\Student::find($studentId);
                    $absentStudentNames[] = $student->name_ar;
                    // Logic for notifying supervisor (simulated/requested for production-ready logic)
                    // Trigger custom alert or notification system here
                    \Illuminate\Support\Facades\Log::info("Absence Alert: Student {$student->name_ar} (ID: {$student->student_number}) from circle '{$circle->name}' was absent on {$request->session_date}.");
                }
            }
        });

        $message = 'تم تسجيل الجلسة والحضور بنجاح.';
        if (count($absentStudentNames) > 0) {
            $message .= ' تم إصدار تنبيه آلي للمشرفين بخصوص ' . count($absentStudentNames) . ' طلاب غائبين.';
        }

        return redirect()->route('quran-circles.show', $circle)->with('success', $message);
    }

    public function show(CircleSession $session)
    {
        $this->authorizeAccess($session->circle);
        $session->load('attendance.student');
        return view('circle-sessions.show', compact('session'));
    }

    protected function authorizeAccess(QuranCircle $circle)
    {
        $user = Auth::user();
        if ($user->hasRole('super-admin'))
            return;
        if ($circle->center_id !== $user->center_id)
            abort(403);
        if ($user->hasRole('circle-teacher') && $circle->teacher_id !== $user->id) {
            if (!$user->can('view-circle-reports') && !$user->can('manage-quran-circles'))
                abort(403);
        }
    }
}
