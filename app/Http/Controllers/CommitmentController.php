<?php

namespace App\Http\Controllers;

use App\Models\Commitment;
use App\Models\Student;
use App\Models\Violation;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class CommitmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Commitment::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation']);

        // Filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commitments = $query->latest()->paginate(20)->withQueryString();

        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        return view('administrative.commitments', compact('commitments', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'                 => 'required',
            'violation_id'               => 'nullable|exists:violations,id',
            'title'                      => 'nullable|string|max:255',
            'text'                       => 'required|string',
            'date'                       => 'required|date',
            'requires_guardian_signature' => 'boolean',
            'image'                      => 'nullable|file|image|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('commitments', 'public');
        }

        $studentIds = is_array($request->student_id) ? $request->student_id : [$request->student_id];

        /** @var WhatsAppService $whatsapp */
        $whatsapp = app(WhatsAppService::class);

        foreach ($studentIds as $id) {
            Commitment::create([
                'student_id'                  => $id,
                'violation_id'                => $validated['violation_id'] ?? null,
                'title'                       => $validated['title'] ?? null,
                'text'                        => $validated['text'],
                'date'                        => $validated['date'],
                'requires_guardian_signature' => $request->has('requires_guardian_signature'),
                'image_path'                  => $imagePath,
                'status'                      => 'active',
            ]);

            // إرسال إشعار واتساب للطالب
            $student = Student::find($id);
            if ($student && $student->phone) {
                /** @var WhatsAppService $whatsapp */
                $whatsapp = app(WhatsAppService::class);
                $message = $whatsapp->commitmentMessage(
                    $student->name_ar,
                    $validated['title'] ?? 'تعهد سلوكي',
                    $validated['date']
                );
                $whatsapp->flash($student->phone, $message, $student->name_ar);
            }
        }

        return back()->with('success', 'تم تسجيل التعهد بنجاح.');
    }

    public function edit(Commitment $commitment)
    {
        $user = auth()->user();
        $students = Student::where('center_id', $user->center_id)
            ->whereIn('status', ['registered', 'residing'])
            ->get(['id', 'name_ar', 'student_number']);

        $violations = Violation::where('center_id', $user->center_id)->latest()->take(50)->get();

        return view('administrative.commitment_edit', compact('commitment', 'students', 'violations'));
    }

    public function update(Request $request, Commitment $commitment)
    {
        $validated = $request->validate([
            'student_id'                 => 'required|exists:students,id',
            'violation_id'               => 'nullable|exists:violations,id',
            'title'                      => 'nullable|string|max:255',
            'text'                       => 'required|string',
            'date'                       => 'required|date',
            'requires_guardian_signature' => 'boolean',
            'image'                      => 'nullable|file|image|max:5120',
            'status'                     => 'required|in:active,expired',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('commitments', 'public');
        }

        $commitment->update([
            'student_id'                  => $validated['student_id'],
            'violation_id'                => $validated['violation_id'] ?? null,
            'title'                       => $validated['title'] ?? null,
            'text'                        => $validated['text'],
            'date'                        => $validated['date'],
            'requires_guardian_signature' => $request->has('requires_guardian_signature'),
            'image_path'                  => $validated['image_path'] ?? $commitment->image_path,
            'status'                      => $validated['status'],
        ]);

        return redirect()->route('administrative.index', ['tab' => 'commitments'])
            ->with('success', 'تم تحديث التعهد بنجاح.');
    }

    public function destroy(Commitment $commitment)
    {
        $commitment->delete();
        return back()->with('success', 'تم حذف التعهد بنجاح.');
    }

    public function exportPdf(Commitment $commitment, \App\Services\PdfService $pdfService)
    {
        $commitment->load(['student', 'violation']);
        return $pdfService->stream('pdf.commitments.show', [
            'commitment' => $commitment,
        ], 'تعهد - ' . $commitment->student->name_ar, 'commitment_' . $commitment->id . '.pdf', 'portrait');
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Commitment::query()
            ->whereHas('student', fn($q) => $q->when($user->center_id, fn($sq) => $sq->where('center_id', $user->center_id)))
            ->with(['student', 'violation']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commitments = $query->latest()->get();

        return $pdfService->stream('pdf.reports.commitments', [
            'data' => $commitments,
        ], 'تقرير التعهدات', 'commitments_report_' . now()->format('Y-m-d') . '.pdf', 'landscape');
    }
}
