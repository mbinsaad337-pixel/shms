<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodQrGroup;
use App\Models\FoodSubscription;
use App\Models\Student;
use Illuminate\Http\Request;

class FoodQrGroupController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        if (!$student)
            return redirect()->route('dashboard');

        $groups = FoodQrGroup::where('created_by_student_id', $student->id)
            ->with('members.student')
            ->latest()
            ->paginate(10);

        return view('nutrition.qr_groups.index', compact('groups'));
    }

    public function create()
    {
        $student = auth()->user()->student;
        if (!$student)
            return redirect()->route('dashboard');

        $centerId = $student->center_id;
        // Get all active subscribers in the same center except self
        $students = FoodSubscription::with('student')
            ->where('center_id', $centerId)
            ->where('status', 'active')
            ->where('student_id', '!=', $student->id)
            ->get()
            ->map(fn($s) => $s->student)
            ->filter()
            ->unique('id')
            ->values();

        return view('nutrition.qr_groups.create', compact('students', 'student'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        $studentSelf = auth()->user()->student;
        if (!$studentSelf)
            return redirect()->route('dashboard');

        // Include self
        $allIds = array_unique(array_merge([$studentSelf->id], $request->student_ids));

        $group = FoodQrGroup::generateForStudents($studentSelf, $allIds, $studentSelf->center_id);

        return redirect()->route('nutrition.qr-groups.show', $group)
            ->with('success', 'تم إنشاء QR المجمع بنجاح. صالح حتى نهاية اليوم.');
    }

    public function show(FoodQrGroup $qrGroup)
    {
        $qrGroup->load(['members.student', 'creatorStudent']);
        return view('nutrition.qr_groups.show', compact('qrGroup'));
    }
}
