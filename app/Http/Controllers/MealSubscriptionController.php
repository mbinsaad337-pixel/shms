<?php

namespace App\Http\Controllers;

use App\Models\MealSubscription;
use App\Models\Student;
use Illuminate\Http\Request;

class MealSubscriptionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subscriptions = MealSubscription::query()
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
            ->with('student')
            ->latest()
            ->paginate(20);

        return view('meals.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $students = Student::where('center_id', auth()->user()->center_id)->get();
        return view('meals.subscriptions.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:full,partial',
            'meal_types' => 'required|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
        ]);

        MealSubscription::create(array_merge($validated, [
            'center_id' => auth()->user()->center_id,
            'created_by' => auth()->id(),
            'status' => 'active',
        ]));

        return redirect()->route('subscriptions.index')->with('success', 'تم تفعيل الاشتراك بنجاح.');
    }
}
