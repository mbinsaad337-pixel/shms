<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Room::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->when($user->hasRole('academic-supervisor') && !$user->hasRole('student-supervisor'), fn($q) => $q->where('building', 'academic'))
            ->when($user->hasRole('cooperative-supervisor') && !$user->hasRole('student-supervisor'), fn($q) => $q->where('building', 'cooperative'))
            ->when($request->status == 'available', fn($q) => $q->where('status', 'available'))
            ->when($request->filled('apartment'), fn($q) => $q->where('apartment', $request->apartment))
            ->when($request->filled('floor'), fn($q) => $q->where('floor', $request->floor))
            ->when($request->has('vacant'), function ($q) {
                return $q->whereRaw('capacity > (select count(*) from room_assignments where room_assignments.room_id = rooms.id and released_at is null)');
            });

        $rooms = $query->withCount([
                'students' => function ($q) {
                    $q->whereNull('released_at');
                }
            ])
            ->with(['students' => function ($q) {
                $q->whereNull('room_assignments.released_at')->select('students.id', 'students.name_ar');
            }])
            ->get();

        // Get unique options for filters for this center
        $apartments = Room::where('center_id', $user->center_id)->whereNotNull('apartment')->where('apartment', '!=', '')->distinct()->pluck('apartment');
        $floors = Room::where('center_id', $user->center_id)->whereNotNull('floor')->where('floor', '!=', '')->distinct()->pluck('floor');

        return view('rooms.index', compact('rooms', 'apartments', 'floors'));
    }

    public function create()
    {
        abort_unless(auth()->user()->can('manage-rooms'), 403, 'ليس لديك صلاحية إضافة غرف.');
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('manage-rooms'), 403, 'ليس لديك صلاحية إضافة غرف.');
        $validated = $request->validate([
            'room_number' => 'required|string',
            'apartment' => 'nullable|string',
            'building' => 'required|in:academic,cooperative',
            'floor' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|in:residential,study_hall,activity_hall,other',
        ]);

        Room::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id,
        ]));

        return redirect()->route('rooms.index')->with('success', 'تم إضافة المرفق بنجاح.');
    }

    public function edit(Room $room)
    {
        abort_unless(auth()->user()->can('manage-rooms'), 403, 'ليس لديك صلاحية تعديل الغرف.');
        if ($room->center_id !== auth()->user()->center_id)
            abort(403);
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        abort_unless(auth()->user()->can('manage-rooms'), 403, 'ليس لديك صلاحية تعديل الغرف.');
        if ($room->center_id !== auth()->user()->center_id)
            abort(403);

        $validated = $request->validate([
            'room_number' => 'required|string',
            'apartment' => 'nullable|string',
            'building' => 'required|in:academic,cooperative',
            'floor' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'type' => 'required|in:residential,study_hall,activity_hall,other',
            'status' => 'required|in:available,maintenance,closed',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'تم تحديث بيانات المرفق بنجاح.');
    }

    public function destroy(Room $room)
    {
        abort_unless(auth()->user()->can('manage-rooms'), 403, 'ليس لديك صلاحية حذف الغرف.');
        if ($room->center_id !== auth()->user()->center_id)
            abort(403);
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'تم حذف المرفق بنجاح.');
    }

    public function vacate(Request $request, Room $room)
    {
        $studentId = $request->student_id;

        $assignment = \App\Models\RoomAssignment::where('room_id', $room->id)
            ->where('student_id', $studentId)
            ->whereNull('released_at')
            ->first();

        if ($assignment) {
            $assignment->update([
                'released_at' => now(),
                'release_reason' => $request->reason ?? 'نهاية الفترة',
            ]);

            return back()->with('success', 'تم إخلاء الطالب من الغرفة بنجاح.');
        }

        return back()->with('error', 'الطالب غير موجود في هذه الغرفة.');
    }

    public function exportPdf(Room $room, \App\Services\PdfService $pdfService)
    {
        $room->load(['assignments' => function ($q) {
            $q->whereNull('released_at')->with('student');
        }]);

        return $pdfService->stream('pdf.rooms.room-pdf', [
            'room' => $room,
        ], 'تقرير الغرفة', 'room_' . $room->room_number . '.pdf', 'portrait');
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Room::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->when($user->hasRole('academic-supervisor') && !$user->hasRole('student-supervisor'), fn($q) => $q->where('building', 'academic'))
            ->when($user->hasRole('cooperative-supervisor') && !$user->hasRole('student-supervisor'), fn($q) => $q->where('building', 'cooperative'))
            ->when($request->status == 'available', fn($q) => $q->where('status', 'available'))
            ->when($request->filled('apartment'), fn($q) => $q->where('apartment', $request->apartment))
            ->when($request->filled('floor'), fn($q) => $q->where('floor', $request->floor))
            ->withCount(['students' => function ($q) {
                $q->whereNull('released_at');
            }]);

        $rooms = $query->get();

        return $pdfService->stream('pdf.rooms.list-pdf', [
            'data' => $rooms,
        ], 'تقرير قائمة الغرف', 'rooms_list_' . now()->format('Y-m-d') . '.pdf', 'landscape');
    }
}
