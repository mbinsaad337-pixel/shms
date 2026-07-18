<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentQrGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentQrGroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'يجب أن تكون طالباً للوصول لهذه الصفحة.');
        }

        $groups = StudentQrGroup::where('primary_student_id', $student->id)
            ->with('students')
            ->latest()
            ->paginate(10);

        return view('student_qr_groups.index', compact('groups'));
    }

    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('dashboard');
        }

        // Get students from the same center who have an active food subscription
        $students = Student::where('center_id', $student->center_id)
            ->where('id', '!=', $student->id)
            ->whereHas('foodSubscriptions', function ($query) {
                $query->where('status', 'active');
            })
            ->with('activeFoodSubscription')
            ->get();

        return view('student_qr_groups.create', compact('students', 'student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $primaryStudent = auth()->user()->student;
        if (!$primaryStudent) {
            return redirect()->route('dashboard');
        }

        $selectedStudents = Student::whereIn('id', $request->student_ids)->get();
        // Always include the primary student in the data
        $allStudentsForData = $selectedStudents->prepend($primaryStudent);

        $studentData = $allStudentsForData->map(function ($s) {
            return [
                'name' => $s->name_ar,
                'id' => $s->university_id,
                'major' => $s->major,
                'college' => $s->college,
                'phone' => $s->phone,
            ];
        });

        $jsonData = [
            'primary_student' => $primaryStudent->name_ar,
            'created_at' => now()->toDateTimeString(),
            'students' => $studentData->toArray(),
        ];

        $jsonString = json_encode($jsonData, JSON_UNESCAPED_UNICODE);
        $isLinkOnly = strlen($jsonString) > 1000;
        $token = Str::random(40);

        $group = StudentQrGroup::create([
            'primary_student_id' => $primaryStudent->id,
            'group_token' => $token,
            'json_data' => $jsonData,
            'is_link_only' => $isLinkOnly,
            'expires_at' => $request->expires_at ?? now()->endOfDay(),
            'status' => 'active',
        ]);

        // Attach members
        $group->students()->attach($request->student_ids);

        return redirect()->route('student-qr-groups.show', $group)
            ->with('success', 'تم إنشاء الرمز المجمع بنجاح.');
    }

    public function show(StudentQrGroup $studentQrGroup)
    {
        $this->authorizeOwner($studentQrGroup);

        $studentQrGroup->load('students', 'primaryStudent');

        // Always use a URL so QR doesn't contain Arabic text (which causes ISO-8859-1 encoding errors).
        // The scan route retrieves and displays the student data from the database.
        $qrData = route('student-qr-groups.scan', $studentQrGroup->group_token);

        return view('student_qr_groups.show', compact('studentQrGroup', 'qrData'));
    }

    public function scan($token)
    {
        $group = StudentQrGroup::where('group_token', $token)
            ->with('students', 'primaryStudent')
            ->firstOrFail();

        if ($group->expires_at && $group->expires_at->isPast()) {
            return view('student_qr_groups.expired', compact('group'));
        }

        return view('student_qr_groups.scan_result', compact('group'));
    }

    public function destroy(StudentQrGroup $studentQrGroup)
    {
        $this->authorizeOwner($studentQrGroup);
        $studentQrGroup->delete();

        return redirect()->route('student-qr-groups.index')
            ->with('success', 'تم حذف الرمز المجمع بنجاح.');
    }

    protected function authorizeOwner(StudentQrGroup $group)
    {
        if ($group->primary_student_id !== auth()->user()->student?->id) {
            abort(403);
        }
    }
}
