<?php

namespace App\Http\Controllers;

use App\Models\MealDistribution;
use App\Models\Student;
use App\Models\MealSubscription;
use Illuminate\Http\Request;

class MealDistributionController extends Controller
{
    public function index()
    {
        $distributions = MealDistribution::with(['student', 'subscription'])
            ->whereDate('distributed_at', now())
            ->latest()
            ->paginate(50);

        return view('meals.distributions.index', compact('distributions'));
    }

    public function scanView()
    {
        return view('meals.distributions.scan');
    }

    public function scan(Request $request)
    {
        $barcode = $request->barcode;
        $student = Student::where('barcode', $barcode)->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'الطالب غير موجود'], 404);
        }

        $subscription = MealSubscription::where('student_id', $student->id)
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();

        if (!$subscription) {
            return response()->json(['success' => false, 'message' => 'الطالب غير مشترك في التغذية'], 403);
        }

        $type = $this->getCurrentMealType();
        $exists = MealDistribution::where('student_id', $student->id)
            ->whereDate('distributed_at', now())
            ->where('meal_type', $type)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'تم استلام الوجبة مسبقاً لهذا اليوم'], 403);
        }

        $distribution = MealDistribution::create([
            'student_id' => $student->id,
            'subscription_id' => $subscription->id,
            'meal_type' => $type,
            'distributed_at' => now(),
            'distributed_by' => auth()->id(),
        ]);

        $mealNames = ['breakfast' => 'فطور', 'lunch' => 'غداء', 'dinner' => 'عشاء'];

        return response()->json([
            'success' => true,
            'student_name' => $student->name_ar,
            'meal_type' => $mealNames[$type],
            'time' => $distribution->distributed_at->format('H:i:s'),
        ]);
    }

    private function getCurrentMealType()
    {
        $hour = date('H');
        if ($hour < 11)
            return 'breakfast';
        if ($hour < 17)
            return 'lunch';
        return 'dinner';
    }
}
