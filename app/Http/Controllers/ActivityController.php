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

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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

        // Fetch distinct categories for filter dropdown
        $categoryQuery = Activity::query();
        if ($user->center_id) {
            $categoryQuery->where('center_id', $user->center_id);
        }
        $categories = $categoryQuery->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('social.activities.index', compact('activities', 'clubs', 'students', 'centers', 'categories'));
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

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $activities  = $query->latest()->get();
        $totalCost   = $activities->whereNotNull('total_cost')->sum('total_cost');
        $centerName  = $user->center_id ? $user->center->name : ($request->filled('center_id') ? \App\Models\Center::find($request->center_id)->name : 'جميع المراكز');

        $extraInfo = ['المركز' => $centerName];
        if ($request->filled('month')) {
            $extraInfo['الشهر'] = \Carbon\Carbon::createFromFormat('Y-m', $request->month)->translatedFormat('F Y');
        }
        if ($request->filled('category')) {
            $extraInfo['الفئة'] = $request->category;
        }

        return $pdfService->stream('pdf.social.activities.list-pdf', [
            'data'       => $activities,
            'totalCost'  => $totalCost,
            'centerName' => $centerName,
        ], 'تقرير الفعاليات والأنشطة', 'activities_list.pdf', 'landscape', $extraInfo);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id'              => 'required|exists:clubs,id',
            'name'                 => 'required|string',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'start_time'           => 'nullable|string',
            'end_time'             => 'nullable|string',
            'location'             => 'required|string',
            'target_all_students'  => 'nullable|boolean',
            'target_student_ids'   => 'nullable|array',
            'target_student_ids.*' => 'exists:students,id',
            'target_club_members'  => 'nullable|boolean',
            'target_audience'      => 'nullable|string',
            'category'             => 'nullable|string|max:255',
            'total_cost'           => 'nullable|numeric|min:0',
            'attachment_pdf'       => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $activityData = array_merge($request->only([
            'club_id', 'name', 'start_date', 'end_date', 'start_time', 'end_time',
            'location', 'target_audience', 'category', 'total_cost',
        ]), [
            'center_id'  => auth()->user()->center_id,
            'created_by' => auth()->id(),
            'status'     => 'planned',
        ]);

        if ($request->hasFile('attachment_pdf')) {
            $activityData['attachment_pdf'] = $request->file('attachment_pdf')
                ->store('activities/attachments', 'public');
        }

        $activity = Activity::create($activityData);

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
            'club_id'              => 'required|exists:clubs,id',
            'name'                 => 'required|string',
            'start_date'           => 'required|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'start_time'           => 'nullable|string',
            'end_time'             => 'nullable|string',
            'location'             => 'required|string',
            'target_student_ids'   => 'nullable|array',
            'target_student_ids.*' => 'exists:students,id',
            'target_club_members'  => 'nullable|boolean',
            'status'               => 'required|in:planned,published,completed,cancelled',
            'target_audience'      => 'nullable|string',
            'category'             => 'nullable|string|max:255',
            'total_cost'           => 'nullable|numeric|min:0',
            'attachment_pdf'       => 'nullable|file|mimes:pdf|max:10240',
            'remove_attachment'    => 'nullable|boolean',
        ]);

        $activityData = $request->only([
            'club_id', 'name', 'start_date', 'end_date', 'start_time', 'end_time',
            'location', 'status', 'target_audience', 'category', 'total_cost',
        ]);

        // Handle PDF attachment upload
        if ($request->hasFile('attachment_pdf')) {
            // Delete old file if exists
            if ($activity->attachment_pdf) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($activity->attachment_pdf);
            }
            $activityData['attachment_pdf'] = $request->file('attachment_pdf')
                ->store('activities/attachments', 'public');
        } elseif ($request->boolean('remove_attachment') && $activity->attachment_pdf) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($activity->attachment_pdf);
            $activityData['attachment_pdf'] = null;
        }

        $activity->update($activityData);

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

    public function updateStatus(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'status' => 'required|in:planned,published,completed,cancelled',
        ]);

        $activity->update(['status' => $validated['status']]);

        return back()->with('success', 'تم تحديث حالة الفعالية بنجاح.');
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
