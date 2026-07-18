<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Violation::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id));

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('violation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('violation_date', '<=', $request->date_to);
        }

        $violations = $query->with(['student', 'recordedBy'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Get unique types for filter
        $types = Violation::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->distinct()
            ->pluck('type');

        return view('violations.index', compact('violations', 'types'));
    }

    public function create()
    {
        $students = Student::where('center_id', auth()->user()->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get();
        return view('violations.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'type' => 'required|string',
            'severity' => 'required|in:minor,moderate,severe',
            'description' => 'required|string',
            'violation_date' => 'required|date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:5120',
            'circle_attendance_id' => 'nullable|array',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('violations/attachments', 'public');
            }
        }

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        foreach ($studentIds as $id) {
            Violation::create([
                'student_id' => $id,
                'type' => $validated['type'],
                'severity' => $validated['severity'],
                'description' => $validated['description'],
                'violation_date' => $validated['violation_date'],
                'attachments' => count($attachmentPaths) ? $attachmentPaths : null,
                'center_id' => auth()->user()->center_id,
                'recorded_by' => auth()->id(),
            ]);
        }

        if (!empty($validated['circle_attendance_id'])) {
            \App\Models\CircleAttendance::whereIn('id', $validated['circle_attendance_id'])->update(['is_handled' => true]);
        }

        return redirect()->route('violations.index')->with('success', 'تم تسجيل المخالفة بنجاح.');
    }
    public function show(Violation $violation)
    {
        $violation->load(['student', 'recordedBy', 'penalty']);
        return view('violations.show', compact('violation'));
    }

    public function destroy(Violation $violation)
    {
        $violation->delete();
        return redirect()->route('violations.index')->with('success', 'تم حذف المخالفة بنجاح.');
    }
    public function exportListPdf(Request $request)
    {
        $user = auth()->user();
        $violations = Violation::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->with(['student', 'recordedBy'])
            ->latest()
            ->get();

        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('violations.reports.list-pdf', compact('violations'), [], [
            'format' => 'A4-L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'auto_language_detection' => true,
            'temp_dir' => storage_path('app/mpdf'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'violations_report_' . now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function exportPdf(Violation $violation)
    {
        $violation->load(['student', 'recordedBy', 'penalty']);
        
        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('violations.reports.show-pdf', compact('violation'), [], [
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'auto_language_detection' => true,
            'temp_dir' => storage_path('app/mpdf'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'violation_' . $violation->id . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
