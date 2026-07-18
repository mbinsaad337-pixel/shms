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

        // For Admin/Supervisor
        $query = StudentAchievement::with('student');
        if (request()->filled('student_id')) {
            $query->where('student_id', request()->student_id);
        }
        $achievements = $query->latest()->paginate(20);
        return view('student_achievements.admin-index', compact('achievements'));
    }

    public function create()
    {
        // Only admin/supervisor can create achievements for students
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }
        $students = Student::active()->get(); // Assuming active() scope exists, if not use all()
        if ($students->isEmpty()) $students = Student::all();

        return view('student_achievements.create', compact('students'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:1000',
            'achievement_date' => 'required|date',
            'certificate_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except('certificate_file');

        if ($request->hasFile('certificate_file')) {
            $data['certificate_file'] = $request->file('certificate_file')->store('students/achievements', 'public');
        }

        StudentAchievement::create($data);

        return redirect()->route('student-achievements.index')->with('success', 'تم إضافة الإنجاز بنجاح.');
    }

    public function edit(StudentAchievement $studentAchievement)
    {
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }
        $students = Student::all();
        return view('student_achievements.edit', compact('studentAchievement', 'students'));
    }

    public function update(Request $request, StudentAchievement $studentAchievement)
    {
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:1000',
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
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }

        if ($studentAchievement->certificate_file) {
            Storage::disk('public')->delete($studentAchievement->certificate_file);
        }
        $studentAchievement->delete();

        return redirect()->route('student-achievements.index')->with('success', 'تم حذف الإنجاز بنجاح.');
    }
}
