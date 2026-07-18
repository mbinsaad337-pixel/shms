<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'date' => 'required|date',
            'has_excuse' => 'required|boolean',
            'excuse_type' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        foreach ($studentIds as $id) {
            Absence::create([
                'student_id' => $id,
                'date' => $validated['date'],
                'has_excuse' => $validated['has_excuse'],
                'excuse_type' => $validated['excuse_type'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'recorded_by' => auth()->id(),
            ]);
        }

        return back()->with('success', 'تم تسجيل الغياب بنجاح.');
    }
}
