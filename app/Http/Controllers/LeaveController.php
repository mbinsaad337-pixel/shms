<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Student;
use App\Models\Violation;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Pending leave requests needing approval (supervisor dashboard)
        $pendingCount = Leave::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->where('status', 'pending')
            ->count();

        $query = Leave::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'approvedBy', 'violation']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('departure_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('departure_date', '<=', $request->date_to);
        }

        $leaves = $query->latest()->paginate(20)->withQueryString();

        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        return view('administrative.leaves', compact('leaves', 'students', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'           => 'required',
            'type'                 => 'required|in:temporary,vacation,medical,lateness',
            'departure_date'       => 'required|date',
            'departure_time'       => 'nullable|date_format:H:i',
            'expected_return_date' => 'nullable|date',
            'expected_return_time' => 'nullable|date_format:H:i',
            'reason'               => 'required|string',
        ]);

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        /** @var WhatsAppService $whatsapp */
        $whatsapp = app(WhatsAppService::class);

        foreach ($studentIds as $id) {
            Leave::create([
                'student_id'           => $id,
                'type'                 => $validated['type'],
                'departure_date'       => $validated['departure_date'],
                'departure_time'       => $validated['departure_time'] ?? null,
                'expected_return_date' => $validated['expected_return_date'] ?? null,
                'expected_return_time' => $validated['expected_return_time'] ?? null,
                'reason'               => $validated['reason'],
                'status'               => 'approved', // Supervisor registers directly = approved
                'approved_by'          => auth()->id(),
                'submitted_by_student' => false,
            ]);

            // إرسال إشعار واتساب للطالب
            $student = Student::find($id);
            if ($student && $student->phone) {
                $message = $whatsapp->leaveApprovalMessage(
                    $student->name_ar,
                    $validated['type'],
                    $validated['departure_date'],
                    $validated['expected_return_date'] ?? null
                );
                $whatsapp->flash($student->phone, $message, $student->name_ar);
            }
        }

        return back()->with('success', 'تم تسجيل الاستئذان بنجاح.');
    }

    public function edit(Leave $leave)
    {
        $user = auth()->user();
        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        return view('administrative.leave_edit', compact('leave', 'students'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'student_id'           => 'required|exists:students,id',
            'type'                 => 'required|in:temporary,vacation,medical,lateness',
            'departure_date'       => 'required|date',
            'departure_time'       => 'nullable|date_format:H:i',
            'expected_return_date' => 'nullable|date',
            'expected_return_time' => 'nullable|date_format:H:i',
            'reason'               => 'required|string',
            'status'               => 'required|in:pending,approved,rejected,returned,not_returned',
            'rejection_reason'     => 'nullable|string',
        ]);

        $leave->update($validated);

        return redirect()->route('administrative.index', ['tab' => 'leaves'])
            ->with('success', 'تم تحديث الاستئذان بنجاح.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return back()->with('success', 'تم حذف الاستئذان بنجاح.');
    }

    /**
     * Supervisor approves a student leave request
     */
    public function approve(Request $request, Leave $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        // إرسال إشعار واتساب للطالب
        $student = $leave->student;
        if ($student && $student->phone) {
            /** @var WhatsAppService $whatsapp */
            $whatsapp = app(WhatsAppService::class);
            $message = $whatsapp->leaveApprovalMessage(
                $student->name_ar,
                $leave->type,
                $leave->departure_date?->format('Y-m-d'),
                $leave->expected_return_date?->format('Y-m-d')
            );
            $whatsapp->flash($student->phone, $message);
        }

        return back()->with('success', 'تم قبول طلب الاستئذان بنجاح.');
    }

    /**
     * Supervisor rejects a student leave request
     */
    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'تم رفض طلب الاستئذان.');
    }

    /**
     * Convert a rejected/unapproved leave to a violation
     */
    public function convertToViolation(Request $request, Leave $leave)
    {
        $request->validate([
            'type'        => 'required|string',
            'severity'    => 'required|in:minor,moderate,severe',
            'description' => 'required|string',
        ]);

        $violation = Violation::create([
            'student_id'     => $leave->student_id,
            'type'           => $request->type,
            'severity'       => $request->severity,
            'description'    => $request->description,
            'violation_date' => $leave->departure_date,
            'center_id'      => auth()->user()->center_id,
            'recorded_by'    => auth()->id(),
        ]);

        $leave->update([
            'converted_to_violation' => true,
            'violation_id'           => $violation->id,
            'status'                 => 'rejected',
        ]);

        return back()->with('success', 'تم تحويل الاستئذان إلى مخالفة وتسجيلها بنجاح.');
    }

    /**
     * Handle Return
     */
    public function return(Leave $leave)
    {
        $leave->update([
            'actual_return_date' => now(),
            'status'             => 'returned',
        ]);

        return back()->with('success', 'تم تسجيل العودة بنجاح.');
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Leave::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'approvedBy']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('departure_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('departure_date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest()->get();

        return $pdfService->stream('pdf.reports.leaves', [
            'data' => $leaves,
        ], 'تقرير الاستئذانات', 'leaves_report_' . now()->format('Y-m-d') . '.pdf', 'landscape');
    }

    public function updateCutoffTime(Request $request)
    {
        $center = auth()->user()->center;
        if (!$center) {
            return back()->with('error', 'ليس لديك صلاحية لتعديل إعدادات المركز.');
        }

        if ($request->has('clear')) {
            $center->update(['leave_cutoff_time' => null]);
            return back()->with('success', 'تم إلغاء وقت المنع للاستئذان بنجاح.');
        }

        $request->validate(['leave_cutoff_time' => 'required|date_format:H:i']);
        $center->update(['leave_cutoff_time' => $request->leave_cutoff_time]);

        return back()->with('success', 'تم حفظ وقت المنع اليومي بنجاح.');
    }
}
