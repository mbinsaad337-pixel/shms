<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Student;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $leaves = Leave::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'approvedBy'])
            ->latest()
            ->paginate(20);

        return view('leaves.index', compact('leaves'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'type' => 'required|in:temporary,vacation,medical',
            'departure_date' => 'required|date',
            'expected_return_date' => 'required|date|after:departure_date',
            'reason' => 'required|string',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        foreach ($studentIds as $id) {
            Leave::create([
                'student_id' => $id,
                'type' => $validated['type'],
                'departure_date' => $validated['departure_date'],
                'expected_return_date' => $validated['expected_return_date'],
                'reason' => $validated['reason'],
                'status' => 'approved', // Auto approved for now
                'approved_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'تم تسجيل طلب الاستئذان بنجاح.');
    }

    public function update(Request $request, Leave $leave)
    {
        // Handle Return
        $leave->update([
            'actual_return_date' => now(),
            'status' => 'returned',
        ]);

        return back()->with('success', 'تم تسجيل العودة بنجاح.');
    }
}
