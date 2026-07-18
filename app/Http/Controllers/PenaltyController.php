<?php

namespace App\Http\Controllers;

use App\Models\Penalty;
use App\Models\Violation;
use App\Models\Student;
use Illuminate\Http\Request;

class PenaltyController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $penalties = Penalty::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation', 'appliedBy'])
            ->latest()
            ->paginate(20);

        return view('penalties.index', compact('penalties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required',
            'violation_id' => 'nullable|exists:violations,id',
            'type' => 'required|in:verbal_warning,written_warning,service_suspension,temporary_suspension,expulsion',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        foreach ($studentIds as $id) {
            Penalty::create([
                'student_id' => $id,
                'violation_id' => $validated['violation_id'] ?? null,
                'type' => $validated['type'],
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'applied_by' => auth()->id(),
                'is_active' => true,
            ]);
        }

        return redirect()->route('penalties.index')->with('success', 'تم تطبيق العقوبات بنجاح.');
    }
    public function create()
    {
        $user = auth()->user();
        $students = \App\Models\Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get();
        
        $violations = \App\Models\Violation::where('center_id', $user->center_id)
            ->with('student:id,name_ar')
            ->latest()
            ->take(50)
            ->get();

        return view('penalties.create', compact('students', 'violations'));
    }

    public function destroy(Penalty $penalty)
    {
        $penalty->delete();
        return back()->with('success', 'تم إلغاء العقوبة بنجاح.');
    }

    public function exportListPdf(Request $request)
    {
        $user = auth()->user();
        $penalties = Penalty::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation', 'appliedBy'])
            ->latest()
            ->get();

        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView('penalties.reports.list-pdf', compact('penalties'), [], [
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
        }, 'penalties_report_' . now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
