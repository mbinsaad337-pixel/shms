<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'violation_id' => 'nullable|exists:violations,id',
            'text' => 'required|string',
            'date' => 'required|date',
            'requires_guardian_signature' => 'boolean',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        foreach ($studentIds as $id) {
            Commitment::create([
                'student_id' => $id,
                'violation_id' => $validated['violation_id'] ?? null,
                'text' => $validated['text'],
                'date' => $validated['date'],
                'requires_guardian_signature' => $request->has('requires_guardian_signature'),
                'status' => 'active',
            ]);
        }

        return back()->with('success', 'تم تسجيل التعهد بنجاح.');
    }
}
