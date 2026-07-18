<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\Student;
use App\Models\RoomAssignment;

class RoomAssignmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'room_id' => 'required|exists:rooms,id',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        // Check capacity
        if ($room->students()->count() >= $room->capacity) {
            return back()->with('error', 'هذه الغرفة مكتملة العدد.');
        }

        // Check if student already assigned
        $existing = RoomAssignment::where('student_id', $validated['student_id'])
            ->whereNull('released_at')
            ->first();

        if ($existing) {
            return back()->with('error', 'الطالب مسجل بالفعل في غرفة أخرى.');
        }

        RoomAssignment::create([
            'student_id' => $validated['student_id'],
            'room_id' => $validated['room_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'تم توزيع الطالب على الغرفة بنجاح.');
    }

    public function transfer(Request $request, Student $student)
    {
        $validated = $request->validate([
            'new_room_id' => 'required|exists:rooms,id',
        ]);

        $newRoom = Room::findOrFail($validated['new_room_id']);

        // Check capacity of new room
        if ($newRoom->students()->count() >= $newRoom->capacity) {
            return back()->with('error', 'الغرفة الجديدة مكتملة العدد.');
        }

        // Release from current room
        $current = RoomAssignment::where('student_id', $student->id)
            ->whereNull('released_at')
            ->first();

        if ($current) {
            $current->update([
                'released_at' => now(),
                'release_reason' => 'نقل إلى غرفة أخرى',
            ]);
        }

        // Assign to new room
        RoomAssignment::create([
            'student_id' => $student->id,
            'room_id' => $validated['new_room_id'],
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'تم نقل الطالب بنجاح.');
    }
}
