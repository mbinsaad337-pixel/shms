<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAchievement;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentAchievementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            $achievements = StudentAchievement::where('student_id', $user->student->id)->latest()->get();
            return view('student_achievements.student-index', compact('achievements'));
        }

        // For Admin/Supervisor — filter by center unless super-admin or executive-manager
        $query = StudentAchievement::with('student')
            ->whereHas('student', function ($q) use ($user) {
                if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id) {
                    $q->where('center_id', $user->center_id);
                }
            });

        if (request()->filled('student_id')) {
            $query->where('student_id', request()->student_id);
        }

        $achievements = $query->latest()->paginate(20);
        return view('student_achievements.admin-index', compact('achievements'));
    }

    public function create()
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            abort(403);
        }

        $students = Student::query()
            ->when(
                !$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id,
                fn ($q) => $q->where('center_id', $user->center_id)
            )
            // student-supervisor sees all programs; academic/cooperative-supervisor filtered below
            ->when($user->hasRole('academic-supervisor') && !$user->hasRole('student-supervisor'), fn ($q) => $q->whereHas('program', fn ($p) => $p->where('code', 'academic')))
            ->when($user->hasRole('cooperative-supervisor') && !$user->hasRole('student-supervisor'), fn ($q) => $q->whereHas('program', fn ($p) => $p->where('code', 'cooperative')))
            ->whereIn('status', ['registered', 'residing', 'active'])
            ->get();

        if ($students->isEmpty()) {
            $students = Student::when(
                !$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id,
                fn ($q) => $q->where('center_id', $user->center_id)
            )->get();
        }

        return view('student_achievements.create', compact('students'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            abort(403);
        }

        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'title'            => 'required|string|max:150',
            'description'      => 'required|string|max:1000',
            'achievement_date' => 'required|date',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Ensure the selected student belongs to the user's center
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id) {
            $student = Student::where('id', $request->student_id)
                ->where('center_id', $user->center_id)
                ->first();
            if (!$student) {
                abort(403, 'لا يمكنك إضافة إنجاز لطالب خارج مركزك.');
            }
        }

        $data = $request->except('certificate_file');

        if ($request->hasFile('certificate_file')) {
            $data['certificate_file'] = $request->file('certificate_file')->store('students/achievements', 'public');
        }

        StudentAchievement::create($data);

        return redirect()->route('student-achievements.index')->with('success', 'تم إضافة الإنجاز بنجاح.');
    }

    public function edit(StudentAchievement $studentAchievement)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            abort(403);
        }

        // Ensure the achievement belongs to a student in the user's center
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id) {
            if ($studentAchievement->student->center_id !== $user->center_id) {
                abort(403, 'لا يمكنك تعديل إنجازات طلاب خارج مركزك.');
            }
        }

        $students = Student::query()
            ->when(
                !$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id,
                fn ($q) => $q->where('center_id', $user->center_id)
            )
            ->get();

        return view('student_achievements.edit', compact('studentAchievement', 'students'));
    }

    public function update(Request $request, StudentAchievement $studentAchievement)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            abort(403);
        }

        // Ensure the achievement belongs to a student in the user's center
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id) {
            if ($studentAchievement->student->center_id !== $user->center_id) {
                abort(403, 'لا يمكنك تعديل إنجازات طلاب خارج مركزك.');
            }
        }

        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'title'            => 'required|string|max:150',
            'description'      => 'required|string|max:1000',
            'achievement_date' => 'required|date',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('certificate_file');

        if ($request->hasFile('certificate_file')) {
            if ($studentAchievement->certificate_file) {
                Storage::disk('public')->delete($studentAchievement->certificate_file);
            }
            $data['certificate_file'] = $request->file('certificate_file')->store('students/achievements', 'public');
        }

        $studentAchievement->update($data);

        return redirect()->route('student-achievements.index')->with('success', 'تم تحديث الإنجاز بنجاح.');
    }

    public function destroy(StudentAchievement $studentAchievement)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            abort(403);
        }

        // Ensure the achievement belongs to a student in the user's center
        if (!$user->hasRole('super-admin') && !$user->hasRole('executive-manager') && $user->center_id) {
            if ($studentAchievement->student->center_id !== $user->center_id) {
                abort(403, 'لا يمكنك حذف إنجازات طلاب خارج مركزك.');
            }
        }

        if ($studentAchievement->certificate_file) {
            Storage::disk('public')->delete($studentAchievement->certificate_file);
        }
        $studentAchievement->delete();

        return redirect()->route('student-achievements.index')->with('success', 'تم حذف الإنجاز بنجاح.');
    }
}
