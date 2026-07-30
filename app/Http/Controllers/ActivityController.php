<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\Student;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Activity::query()
            ->with(['club', 'participants', 'targetedStudents']);

        // Center Filter
        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        // Month Filter
        if ($request->filled('month')) {
            $monthParts = explode('-', $request->month);
            if (count($monthParts) == 2) {
                $query->whereYear('start_date', $monthParts[0])
                      ->whereMonth('start_date', $monthParts[1]);
            }
        }

        $activities = $query->latest()->paginate(20)->withQueryString();

        $centers = [];
        if (!$user->center_id) {
            $centers = \App\Models\Center::all();
        }

        $clubs = [];
        if ($user->center_id) {
            $clubs = \App\Models\Club::where('center_id', $user->center_id)
                ->where('status', 'active')
                ->get();
        }

        $students = [];
        if ($user->center_id) {
            $students = Student::where('center_id', $user->center_id)
                ->whereIn('status', ['registered', 'residing'])
                ->get();
        }

        return view('social.activities.index', compact('activities', 'clubs', 'students', 'centers'));
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Activity::query()->with(['club', 'participants']);

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('month')) {
            $monthParts = explode('-', $request->month);
            if (count($monthParts) == 2) {
                $query->whereYear('start_date', $monthParts[0])
                      ->whereMonth('start_date', $monthParts[1]);
            }
        }

        $activities = $query->latest()->get();
        $centerName = $user->center_id ? $user->center->name : ($request->filled('center_id') ? \App\Models\Center::find($request->center_id)->name : 'جميع المراكز');

        return $pdfService->stream('pdf.social.activities.list-pdf', [
            'data' => $activities,
        ], 'تقرير الفعاليات والأنشطة', 'activities_list.pdf', 'landscape', ['المركز' => $centerName]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'location' => 'required|string',
            'target_all_students' => 'nullable|boolean',
            'target_student_ids' => 'nullable|array',
            'target_student_ids.*' => 'exists:students,id',
            'target_club_members' => 'nullable|boolean',
        ]);

        $activity = Activity::create(array_merge($request->only([
            'club_id', 'name', 'start_date', 'end_date', 'start_time', 'end_time', 'location'
        ]), [
            'center_id' => auth()->user()->center_id,
            'created_by' => auth()->id(),
            'status' => 'planned'
        ]));

        if ($request->target_all_students) {
            $allStudents = Student::where('center_id', auth()->user()->center_id)
                ->whereIn('status', ['registered', 'residing'])
                ->pluck('id')->toArray();
            $activity->targetedStudents()->sync($allStudents);
        } elseif ($request->target_club_members && $request->club_id) {
            $clubMembers = \App\Models\ClubMember::where('club_id', $request->club_id)->pluck('student_id')->toArray();
            $activity->targetedStudents()->sync($clubMembers);
        } elseif ($request->target_student_ids) {
            $activity->targetedStudents()->sync($request->target_student_ids);
        }

        // If max_participants was null but we have targets, update it for better tracking
        if ($activity->targetedStudents->count() > 0) {
            $activity->update(['max_participants' => $activity->targetedStudents->count()]);
        }

        return back()->with('success', 'تم جدولة الفعالية بنجاح.');
    }

    public function register(Request $request, Activity $activity)
    {
        $student = Student::where('barcode', $request->barcode)->first();
        if (!$student)
            return back()->with('error', 'الطالب غير موجود');

        ActivityParticipant::firstOrCreate([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
        ], ['registered_at' => now()]);

        return back()->with('success', 'تم تسجيل الطالب في الفعالية.');
    }

    public function show(Activity $activity)
    {
        $activity->load(['club', 'participants.student', 'creator', 'targetedStudents']);
        return view('social.activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $centerId = auth()->user()->center_id;
        $clubs = \App\Models\Club::where('center_id', $centerId)
            ->where('status', 'active')
            ->get();
        $students = Student::where('center_id', $centerId)
            ->whereIn('status', ['registered', 'residing'])
            ->get();
        $activity->load('targetedStudents');
        
        return view('social.activities.edit', compact('activity', 'clubs', 'students'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'club_id' => 'required|exists:clubs,id',
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'location' => 'required|string',
            'target_student_ids' => 'nullable|array',
            'target_student_ids.*' => 'exists:students,id',
            'target_club_members' => 'nullable|boolean',
            'status' => 'required|in:planned,published,completed,cancelled'
        ]);

        $activity->update(array_merge($request->only([
            'club_id', 'name', 'start_date', 'end_date', 'start_time', 'end_time', 'location', 'status'
        ])));

        if ($request->target_all_students) {
            $allStudents = Student::where('center_id', auth()->user()->center_id)
                ->whereIn('status', ['registered', 'residing'])
                ->pluck('id')->toArray();
            $activity->targetedStudents()->sync($allStudents);
        } elseif ($request->target_club_members && $request->club_id) {
            $clubMembers = \App\Models\ClubMember::where('club_id', $request->club_id)->pluck('student_id')->toArray();
            $activity->targetedStudents()->sync($clubMembers);
        } else {
            $activity->targetedStudents()->sync($request->target_student_ids ?? []);
        }

        $activity->update(['max_participants' => $activity->targetedStudents->count()]);

        return redirect()->route('activities.show', $activity->id)->with('success', 'تم تحديث الفعالية بنجاح.');
    }

    public function destroy(Activity $activity)
    {
        $activity->participants()->delete();
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'تم حذف الفعالية بنجاح.');
    }

    public function exportAbsenteesPdf(Activity $activity, \App\Services\PdfService $pdfService)
    {
        $activity->load(['targetedStudents', 'participants.student']);
        
        $participantIds = $activity->participants->pluck('student_id')->toArray();
        $absentees = $activity->targetedStudents->filter(function($student) use ($participantIds) {
            return !in_array($student->id, $participantIds);
        });

        return $pdfService->stream('pdf.social.activities.absentees-pdf', [
            'activity' => $activity,
            'absentees' => $absentees,
        ], 'تقرير الغياب عن الفعالية', 'activity_absentees_' . $activity->id . '.pdf', 'portrait');
    }
}
