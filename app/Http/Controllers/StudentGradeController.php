<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentGrade;
use Illuminate\Support\Facades\Storage;

class StudentGradeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            $grades = StudentGrade::where('student_id', $user->student->id)->latest()->get();
            return view('student_grades.student-index', compact('grades'));
        }

        // For Admin/Supervisor - with filtering
        $query = StudentGrade::with('student');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('student', function ($q) use ($searchTerm) {
                $q->where('name_ar', 'like', '%' . $searchTerm . '%')
                  ->orWhere('student_number', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('semester') && $request->semester != '') {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('year')) {
            $query->where('academic_year', 'like', '%' . $request->year . '%');
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $grades = $query->latest()->paginate(20)->withQueryString();
        return view('student_grades.admin-index', compact('grades'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole('student')) {
            return redirect()->route('student-grades.index');
        }
        return view('student_grades.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('student')) {
            abort(403);
        }

        $request->validate([
            'grade_file' => 'required|image|max:2048',
            'semester' => 'required|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'gpa_percentage' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string|max:255',
        ]);

        $filePath = $request->file('grade_file')->store('students/grades', 'public');

        StudentGrade::create([
            'student_id' => $user->student->id,
            'file_path' => $filePath,
            'semester' => $request->semester,
            'academic_year' => $request->academic_year,
            'gpa_percentage' => $request->gpa_percentage,
            'notes' => $request->notes,
        ]);

        return redirect()->route('student-grades.index')->with('success', 'تم رفع بيان الدرجات بنجاح.');
    }

    public function edit(StudentGrade $studentGrade)
    {
        // Only admin/supervisor can edit
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }
        return view('student_grades.edit', compact('studentGrade'));
    }

    public function update(Request $request, StudentGrade $studentGrade)
    {
        if (auth()->user()->hasRole('student')) {
            abort(403);
        }

        $request->validate([
            'semester' => 'required|string|max:50',
            'academic_year' => 'nullable|string|max:20',
            'gpa_percentage' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string|max:255',
            'grade_file' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['semester', 'academic_year', 'gpa_percentage', 'notes']);

        if ($request->hasFile('grade_file')) {
            Storage::disk('public')->delete($studentGrade->file_path);
            $data['file_path'] = $request->file('grade_file')->store('students/grades', 'public');
        }

        $studentGrade->update($data);

        return redirect()->route('student-grades.index')->with('success', 'تم تحديث البيانات بنجاح.');
    }

    public function destroy(StudentGrade $studentGrade)
    {
        // Only admin/supervisor can delete
        if (auth()->user()->hasRole('student')) {
            abort(403, 'لا تملك صلاحية الحذف بعد الرفع.');
        }

        Storage::disk('public')->delete($studentGrade->file_path);
        $studentGrade->delete();

        return redirect()->route('student-grades.index')->with('success', 'تم حذف بيان الدرجات بنجاح.');
    }
}
