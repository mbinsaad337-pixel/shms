<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\MealSchedule;
use Illuminate\Http\Request;

class MealScheduleController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $schedules = MealSchedule::where('center_id', $centerId)->get()->keyBy('meal_type');

        // Define default meal types if not exists
        $mealTypes = [
            'breakfast' => 'الفطور',
            'lunch' => 'الغداء',
            'dinner' => 'العشاء',
        ];

        return view('nutrition.schedules.index', compact('schedules', 'mealTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'late_deadline' => 'required|date_format:H:i',
            'absent_deadline' => 'required|date_format:H:i',
        ]);

        $centerId = auth()->user()->center_id;

        MealSchedule::updateOrCreate(
            ['center_id' => $centerId, 'meal_type' => $request->meal_type],
            $request->only(['start_time', 'end_time', 'late_deadline', 'absent_deadline'])
        );

        return back()->with('success', 'تم تحديث توقيت الوجبة بنجاح.');
    }
}
