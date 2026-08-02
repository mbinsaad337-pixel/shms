<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Show the student's leave/istizhan requests
     */
    public function index()
    {
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'ليس لديك حساب طالب مرتبط.');
        }

        $leaves = Leave::where('student_id', $student->id)
            ->with('approvedBy')
            ->latest()
            ->paginate(15);

        // Pending circle absences (for displaying alerts)
        $pendingAbsences = \App\Models\CircleAttendance::where('is_handled', false)
            ->where('student_id', $student->id)
            ->with(['session.circle'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.leave_requests', compact('leaves', 'pendingAbsences'));
    }

    /**
     * Student submits a leave request (pending supervisor approval)
     */
    public function store(Request $request)
    {
        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'ليس لديك حساب طالب مرتبط.');
        }

        $center = $student->center;
        if ($center && $center->leave_cutoff_time) {
            $cutoff = \Carbon\Carbon::parse($center->leave_cutoff_time);
            if (now()->format('H:i') >= $cutoff->format('H:i')) {
                return back()->with('error', 'عذراً، لقد تجاوزت الوقت المسموح لتقديم طلبات الاستئذان لهذا اليوم (' . $cutoff->format('h:i A') . ').');
            }
        }

        $validated = $request->validate([
            'type'                 => 'required|in:temporary,vacation,medical,lateness',
            'departure_date'       => 'required|date',
            'departure_time'       => 'nullable|date_format:H:i',
            'expected_return_date' => 'nullable|date',
            'expected_return_time' => 'nullable|date_format:H:i',
            'reason'               => 'required|string|max:1000',
        ]);

        Leave::create([
            'student_id'           => $student->id,
            'type'                 => $validated['type'],
            'departure_date'       => $validated['departure_date'],
            'departure_time'       => $validated['departure_time'] ?? null,
            'expected_return_date' => $validated['expected_return_date'] ?? null,
            'expected_return_time' => $validated['expected_return_time'] ?? null,
            'reason'               => $validated['reason'],
            'status'               => 'pending', // Awaiting supervisor approval
            'submitted_by_student' => true,
        ]);

        return back()->with('success', 'تم إرسال طلب الاستئذان إلى المشرف بنجاح، يرجى انتظار الموافقة.');
    }
}
