<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Absence::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'recorder']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('has_excuse')) {
            $query->where('has_excuse', $request->has_excuse == '1');
        }
        if ($request->filled('absence_type')) {
            $query->where('absence_type', $request->absence_type);
        }

        $absences = $query->latest()->paginate(20)->withQueryString();

        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        return view('administrative.absences', compact('absences', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'   => 'required',
            'date'         => 'required|date',
            'absence_type' => 'nullable|string',
            'has_excuse'   => 'required|boolean',
            'excuse_type'  => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        /** @var \App\Services\WhatsAppService $whatsapp */
        $whatsapp = app(\App\Services\WhatsAppService::class);

        foreach ($studentIds as $id) {
            Absence::create([
                'student_id'   => $id,
                'date'         => $validated['date'],
                'absence_type' => $validated['absence_type'] ?? null,
                'has_excuse'   => $validated['has_excuse'],
                'excuse_type'  => $validated['excuse_type'] ?? null,
                'notes'        => $validated['notes'] ?? null,
                'recorded_by'  => auth()->id(),
            ]);

            // إرسال إشعار واتساب للطالب
            $student = Student::find($id);
            if ($student && $student->phone) {
                $message = $whatsapp->absenceMessage(
                    $student->name_ar,
                    $validated['date'],
                    (bool) $validated['has_excuse']
                );
                $whatsapp->flash($student->phone, $message, $student->name_ar);
            }
        }

        return back()->with('success', 'تم تسجيل الغياب بنجاح.');
    }

    public function edit(Absence $absence)
    {
        $user = auth()->user();
        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        return view('administrative.absence_edit', compact('absence', 'students'));
    }

    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'date'         => 'required|date',
            'absence_type' => 'nullable|string',
            'has_excuse'   => 'required|boolean',
            'excuse_type'  => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        $absence->update([
            'student_id'   => $validated['student_id'],
            'date'         => $validated['date'],
            'absence_type' => $validated['absence_type'] ?? null,
            'has_excuse'   => $validated['has_excuse'],
            'excuse_type'  => $validated['excuse_type'] ?? null,
            'notes'        => $validated['notes'] ?? null,
        ]);

        return redirect()->route('administrative.index', ['tab' => 'absences'])
            ->with('success', 'تم تحديث سجل الغياب بنجاح.');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return back()->with('success', 'تم حذف سجل الغياب بنجاح.');
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Absence::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'recorder']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('has_excuse')) {
            $query->where('has_excuse', $request->has_excuse == '1');
        }
        if ($request->filled('type')) {
            $query->where('absence_type', $request->type);
        }

        $absences = $query->latest()->get();

        return $pdfService->stream('pdf.reports.absences', [
            'data' => $absences,
        ], 'تقرير الغيابات', 'absences_report_' . now()->format('Y-m-d') . '.pdf', 'landscape');
    }
}
