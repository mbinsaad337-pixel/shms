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

        return redirect()->route('administrative.index', ['tab' => 'penalties'])->with('success', 'تم تطبيق العقوبات بنجاح.');
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

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Penalty::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation', 'appliedBy']);

        if ($request->filled('student_id')) {
            $query->whereHas('student', fn($q) => $q->where('id', $request->student_id));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $penalties = $query->latest()->get();

        return $pdfService->stream('pdf.reports.penalties', [
            'data' => $penalties,
        ], 'تقرير العقوبات', 'penalties_report_' . now()->format('Y-m-d') . '.pdf', 'landscape');
    }
}
