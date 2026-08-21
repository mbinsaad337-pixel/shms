<?php

namespace App\Http\Controllers;

use App\Models\QuranCircle;
use App\Models\Student;
use App\Models\User;
use App\Models\CircleAttendance;
use App\Models\CircleSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\PdfService;

class QuranCircleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = QuranCircle::with(['teacher', 'center'])->withCount('students');

        // Teachers only see their own circles, unless they have management/report permissions
        if ($user->hasRole('circle-teacher') && !$user->can('manage-quran-circles') && !$user->can('view-circle-reports')) {
            $query->where('teacher_id', $user->id);
        } else {
            // Managers see all circles in their center
            $query->where('center_id', $user->center_id);
        }

        $circles = $query->latest()->paginate(10);

        return view('quran-circles.index', compact('circles'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('circle-teacher') && !$user->can('manage-quran-circles')) {
            abort(403, 'ليس لديك صلاحية لإضافة حلقات جديدة.');
        }

        $center_id = $user->center_id;
        $teachers = User::where('center_id', $center_id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['circle-teacher', 'supervisor', 'center-manager']);
            })->get();
            
        if ($teachers->isEmpty()) {
            $teachers = User::where('center_id', $center_id)->get();
        }

        return view('quran-circles.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('circle-teacher') && !$user->can('manage-quran-circles')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:memorization,recitation',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
        ]);

        QuranCircle::create([
            'center_id' => Auth::user()->center_id,
            'teacher_id' => $request->teacher_id,
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
        ]);

        return redirect()->route('quran-circles.index')->with('success', 'تم إنشاء الحلقة بنجاح');
    }

    public function edit(QuranCircle $quranCircle)
    {
        $user = Auth::user();
        if ($user->hasRole('circle-teacher') && !$user->can('manage-quran-circles')) {
            abort(403, 'لا يمكنك تعديل بيانات الحلقات.');
        }

        $this->authorizeAccess($quranCircle);
        $center_id = $user->center_id;
        $teachers = User::where('center_id', $center_id)
            ->whereHas('roles', function($q) {
                $q->whereIn('name', ['circle-teacher', 'supervisor', 'center-manager']);
            })->get();

        if ($teachers->isEmpty()) {
            $teachers = User::where('center_id', $center_id)->get();
        }

        return view('quran-circles.edit', compact('quranCircle', 'teachers'));
    }

    public function update(Request $request, QuranCircle $quranCircle)
    {
        $user = Auth::user();
        if ($user->hasRole('circle-teacher') && !$user->can('manage-quran-circles')) {
            abort(403);
        }

        $this->authorizeAccess($quranCircle);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:memorization,recitation',
            'description' => 'nullable|string',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $quranCircle->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('quran-circles.index')->with('success', 'تم تحديث الحلقة بنجاح');
    }

    public function show(QuranCircle $quranCircle)
    {
        $this->authorizeAccess($quranCircle);

        $quranCircle->load([
            'students',
            'sessions' => function ($q) {
                $q->latest()->take(5);
            }
        ]);

        return view('quran-circles.show', compact('quranCircle'));
    }

    public function students(QuranCircle $quranCircle)
    {
        $this->authorizeAccess($quranCircle);

        $currentStudentIds = $quranCircle->students()->pluck('students.id')->toArray();

        // available students in the same center not already in this circle
        $availableStudents = Student::where('center_id', $quranCircle->center_id)
            ->whereNotIn('id', $currentStudentIds)
            ->get();

        return view('quran-circles.students', compact('quranCircle', 'availableStudents'));
    }

    public function addStudent(Request $request, QuranCircle $quranCircle)
    {
        $this->authorizeAccess($quranCircle);

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $quranCircle->students()->syncWithoutDetaching([$request->student_id]);

        return back()->with('success', 'تم إضافة الطالب للحلقة');
    }

    public function removeStudent(QuranCircle $quranCircle, Student $student)
    {
        $this->authorizeAccess($quranCircle);

        $quranCircle->students()->detach($student->id);

        return back()->with('success', 'تم إزالة الطالب من الحلقة');
    }

    public function stats(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('view-circle-reports')) {
            abort(403);
        }

        $center_id = $user->center_id;
        $circles = QuranCircle::where('center_id', $center_id)->withCount('students')->get();

        // Build date/circle scoping closure for sessions
        $sessionScope = function ($q) use ($center_id, $request) {
            $q->whereHas('circle', function ($cq) use ($center_id, $request) {
                $cq->where('center_id', $center_id);
                if ($request->filled('circle_id')) {
                    $cq->where('id', $request->circle_id);
                }
            });
            if ($request->filled('start_date')) {
                $q->where('session_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->where('session_date', '<=', $request->end_date);
            }
        };

        // Total circles (not affected by date range, only by circle filter)
        if ($request->filled('circle_id')) {
            $totalCircles = QuranCircle::where('center_id', $center_id)->where('id', $request->circle_id)->count();
        } else {
            $totalCircles = QuranCircle::where('center_id', $center_id)->count();
        }

        // Total sessions in scope
        $totalSessions = CircleSession::where($sessionScope)->count();

        // Total attendance records in scope
        $totalPresent = CircleAttendance::where('status', 'present')
            ->whereHas('session', $sessionScope)->count();
        $totalAbsent = CircleAttendance::where('status', 'absent')
            ->whereHas('session', $sessionScope)->count();
        $totalAttendanceRecords = $totalPresent + $totalAbsent;
        $commitmentRate = $totalAttendanceRecords > 0
            ? round(($totalPresent / $totalAttendanceRecords) * 100)
            : 0;

        // Latest absent records
        $latestAbsences = CircleAttendance::with(['student', 'session.circle'])
            ->where('status', 'absent')
            ->whereHas('session', $sessionScope)
            ->latest()
            ->take(5)
            ->get();

        // Most committed (top students with 'present' status)
        $mostCommitted = CircleAttendance::select('student_id', DB::raw('count(*) as attendance_count'))
            ->where('status', 'present')
            ->whereHas('session', $sessionScope)
            ->groupBy('student_id')
            ->orderByDesc('attendance_count')
            ->with('student')
            ->take(5)
            ->get();

        // Most absent (top students with 'absent' status) 
        $mostAbsent = CircleAttendance::select('student_id', DB::raw('count(*) as absence_count'))
            ->where('status', 'absent')
            ->whereHas('session', $sessionScope)
            ->groupBy('student_id')
            ->orderByDesc('absence_count')
            ->with('student')
            ->take(5)
            ->get();

        // Per-circle breakdown
        $circleStats = [];
        $targetCircles = $request->filled('circle_id')
            ? $circles->where('id', $request->circle_id)
            : $circles;

        foreach ($targetCircles as $circle) {
            $circleSessions = CircleSession::where('circle_id', $circle->id);
            if ($request->filled('start_date')) {
                $circleSessions->where('session_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $circleSessions->where('session_date', '<=', $request->end_date);
            }
            $sessionIds = $circleSessions->pluck('id');

            $cPresent = CircleAttendance::whereIn('session_id', $sessionIds)->where('status', 'present')->count();
            $cAbsent = CircleAttendance::whereIn('session_id', $sessionIds)->where('status', 'absent')->count();
            $cTotal = $cPresent + $cAbsent;

            $circleStats[] = [
                'circle' => $circle,
                'sessions_count' => $sessionIds->count(),
                'students_count' => $circle->students_count,
                'present_count' => $cPresent,
                'absent_count' => $cAbsent,
                'rate' => $cTotal > 0 ? round(($cPresent / $cTotal) * 100) : 0,
            ];
        }

        return view('quran-circles.stats', compact(
            'totalCircles', 'totalSessions', 'totalPresent', 'totalAbsent',
            'commitmentRate', 'latestAbsences', 'mostCommitted', 'mostAbsent',
            'circles', 'circleStats'
        ));
    }

    public function exportStats(Request $request, PdfService $pdfService)
    {
        $user = Auth::user();
        if (!$user->can('view-circle-reports')) {
            abort(403);
        }

        $center_id = $user->center_id;
        $allCircles = QuranCircle::where('center_id', $center_id)->withCount('students')->get();

        $sessionScope = function ($q) use ($center_id, $request) {
            $q->whereHas('circle', function ($cq) use ($center_id, $request) {
                $cq->where('center_id', $center_id);
                if ($request->filled('circle_id')) {
                    $cq->where('id', $request->circle_id);
                }
            });
            if ($request->filled('start_date')) {
                $q->where('session_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $q->where('session_date', '<=', $request->end_date);
            }
        };

        if ($request->filled('circle_id')) {
            $totalCircles = 1;
        } else {
            $totalCircles = $allCircles->count();
        }

        $totalSessions = CircleSession::where($sessionScope)->count();
        $totalPresent = CircleAttendance::where('status', 'present')->whereHas('session', $sessionScope)->count();
        $totalAbsent = CircleAttendance::where('status', 'absent')->whereHas('session', $sessionScope)->count();
        $totalAttendanceRecords = $totalPresent + $totalAbsent;
        $commitmentRate = $totalAttendanceRecords > 0 ? round(($totalPresent / $totalAttendanceRecords) * 100) : 0;

        // Most committed
        $mostCommitted = CircleAttendance::select('student_id', DB::raw('count(*) as attendance_count'))
            ->where('status', 'present')
            ->whereHas('session', $sessionScope)
            ->groupBy('student_id')
            ->orderByDesc('attendance_count')
            ->with('student')
            ->take(10)
            ->get();

        // Most absent
        $mostAbsent = CircleAttendance::select('student_id', DB::raw('count(*) as absence_count'))
            ->where('status', 'absent')
            ->whereHas('session', $sessionScope)
            ->groupBy('student_id')
            ->orderByDesc('absence_count')
            ->with('student')
            ->take(10)
            ->get();

        // Per-circle breakdown
        $circleStats = [];
        $targetCircles = $request->filled('circle_id')
            ? $allCircles->where('id', $request->circle_id)
            : $allCircles;

        foreach ($targetCircles as $circle) {
            $circleSessions = CircleSession::where('circle_id', $circle->id);
            if ($request->filled('start_date')) {
                $circleSessions->where('session_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $circleSessions->where('session_date', '<=', $request->end_date);
            }
            $sessionIds = $circleSessions->pluck('id');
            $cPresent = CircleAttendance::whereIn('session_id', $sessionIds)->where('status', 'present')->count();
            $cAbsent = CircleAttendance::whereIn('session_id', $sessionIds)->where('status', 'absent')->count();
            $cTotal = $cPresent + $cAbsent;

            $circleStats[] = [
                'name' => $circle->name,
                'teacher' => $circle->teacher->name ?? '—',
                'type' => $circle->type == 'memorization' ? 'تحفيظ' : 'تلاوة',
                'sessions_count' => $sessionIds->count(),
                'students_count' => $circle->students_count,
                'present_count' => $cPresent,
                'absent_count' => $cAbsent,
                'rate' => $cTotal > 0 ? round(($cPresent / $cTotal) * 100) : 0,
            ];
        }

        $appliedFilters = [];
        if ($request->filled('circle_id')) {
            $circleName = $allCircles->firstWhere('id', $request->circle_id)?->name ?? '';
            $appliedFilters['الحلقة'] = $circleName;
        }
        if ($request->start_date) $appliedFilters['من تاريخ'] = $request->start_date;
        if ($request->end_date) $appliedFilters['إلى تاريخ'] = $request->end_date;

        $stats = [
            'إجمالي الحلقات' => $totalCircles,
            'إجمالي الجلسات' => $totalSessions,
            'نسبة الالتزام' => $commitmentRate . '%',
            'إجمالي الغيابات' => $totalAbsent,
        ];

        return $pdfService->stream('pdf.quran-circles.stats-report', [
            'data' => $circleStats,
            'mostCommitted' => $mostCommitted,
            'mostAbsent' => $mostAbsent,
            'totalPresent' => $totalPresent,
            'totalAbsent' => $totalAbsent,
            'commitmentRate' => $commitmentRate,
        ], 'تقرير إحصائيات الحلقات القرآنية', 'quran_circles_stats_' . date('Y-m-d') . '.pdf', 'portrait', $appliedFilters, $stats);
    }

    public function absentReport(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('view-circle-reports')) {
            abort(403);
        }

        $center_id = $user->center_id;

        $query = CircleAttendance::with(['student', 'session.circle'])
            ->where('status', 'absent')
            ->whereHas('session.circle', function ($q) use ($center_id) {
                $q->where('center_id', $center_id);
            });

        // Optional filters
        if ($request->circle_id) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('circle_id', $request->circle_id);
            });
        }
        
        if ($request->start_date) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('session_date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('session_date', '<=', $request->end_date);
            });
        }

        $absences = $query->latest()->paginate(20);
        $circles = QuranCircle::where('center_id', $center_id)->get();

        return view('quran-circles.reports.absent_students', compact('absences', 'circles'));
    }

    public function exportAbsentReport(Request $request, PdfService $pdfService)
    {
        $user = Auth::user();
        if (!$user->can('view-circle-reports')) {
            abort(403);
        }

        $center_id = $user->center_id;
        $query = CircleAttendance::with(['student', 'session.circle'])
            ->where('status', 'absent')
            ->whereHas('session.circle', function ($q) use ($center_id) {
                $q->where('center_id', $center_id);
            });

        if ($request->circle_id) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('circle_id', $request->circle_id);
            });
        }
        
        if ($request->start_date) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('session_date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('session', function ($q) use ($request) {
                $q->where('session_date', '<=', $request->end_date);
            });
        }

        $absences = $query->get();
        $appliedFilters = [];
        if ($request->start_date) $appliedFilters['من تاريخ'] = $request->start_date;
        if ($request->end_date) $appliedFilters['إلى تاريخ'] = $request->end_date;

        return $pdfService->stream('pdf.quran-circles.absent-report', [
            'data' => $absences,
        ], 'تقرير الغياب - الحلقات القرآنية', 'absent_students_report_' . date('Y-m-d') . '.pdf', 'portrait', $appliedFilters);
    }

    public function destroy(QuranCircle $quran_circle)
    {
        $user = Auth::user();
        if (!$user->can('manage-quran-circles')) {
            abort(403);
        }

        $this->authorizeAccess($quran_circle);

        $quran_circle->delete();

        return redirect()->route('quran-circles.index')->with('success', 'تم حذف الحلقة القرآنية بنجاح.');
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
