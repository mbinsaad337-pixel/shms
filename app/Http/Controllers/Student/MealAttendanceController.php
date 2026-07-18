<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FoodAttendanceReport;
use App\Models\FoodDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MealAttendanceController extends Controller
{
    /** Meal start times */
    protected $mealTimes = [
        'breakfast' => '07:00',
        'lunch' => '12:30',
        'dinner' => '18:30',
    ];

    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        if (!$student)
            abort(404);

        $todayMeals = $this->getTodayMeals($student);

        return view('student.meals.attendance', compact('todayMeals'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'status' => 'required|in:normal,late,absent',
        ]);

        $student = auth()->user()->student;
        if (!$student)
            abort(403);

        $mealType = $request->meal_type;
        $status = $request->status;
        $today = today();

        // 1. Check if distribution has started
        $distributionStarted = FoodDistribution::where('center_id', $student->center_id)
            ->where('meal_type', $mealType)
            ->whereDate('distributed_at', $today)
            ->exists();

        if ($distributionStarted) {
            return back()->with('error', 'لا يمكن تعديل الحالة بعد بدء توزيع الوجبة.');
        }

        // 2. Check Schedule & Deadlines
        $schedule = \App\Models\MealSchedule::where('center_id', $student->center_id)
            ->where('meal_type', $mealType)
            ->first();

        if ($schedule) {
            if ($status === 'late') {
                $deadline = Carbon::today()->setTimeFromTimeString($schedule->late_deadline);
                if (now()->gt($deadline)) {
                    return back()->with('error', 'انتهى الوقت المسموح للإشعار بالتأخر لهذه الوجبة (' . $schedule->late_deadline . ').');
                }
            } elseif ($status === 'absent') {
                $deadline = Carbon::today()->setTimeFromTimeString($schedule->absent_deadline);
                if (now()->gt($deadline)) {
                    return back()->with('error', 'انتهى الوقت المسموح للإشعار بالغياب لهذه الوجبة (' . $schedule->absent_deadline . ').');
                }
            }
        } else {
            // Fallback to 15-minute rule if no schedule defined
            $mealTimeStr = $this->mealTimes[$mealType];
            $mealTimeFull = Carbon::today()->setTimeFromTimeString($mealTimeStr);
            if (now()->diffInMinutes($mealTimeFull, false) < -15) {
                return back()->with('error', 'لا يمكن الإشعار بعد مرور 15 دقيقة من وقت الوجبة.');
            }
        }

        // 3. Save/Update
        FoodAttendanceReport::updateOrCreate(
            [
                'student_id' => $student->id,
                'meal_date' => $today,
                'meal_type' => $mealType,
            ],
            ['status' => $status]
        );

        return back()->with('success', 'تم تحديث حالة الحضور بنجاح.');
    }

    public function getTodayMeals($student)
    {
        $meals = [];
        $today = today();
        $reports = FoodAttendanceReport::where('student_id', $student->id)
            ->where('meal_date', $today)
            ->get()
            ->keyBy('meal_type');

        $schedules = \App\Models\MealSchedule::where('center_id', $student->center_id)->get()->keyBy('meal_type');

        foreach (['breakfast', 'lunch', 'dinner'] as $type) {
            $report = $reports->get($type);
            $schedule = $schedules->get($type);

            $startTime = $schedule ? $schedule->start_time : $this->mealTimes[$type];
            $lateDeadline = $schedule ? $schedule->late_deadline : Carbon::today()->setTimeFromTimeString($startTime)->addMinutes(15)->format('H:i');
            $absentDeadline = $schedule ? $schedule->absent_deadline : Carbon::today()->setTimeFromTimeString($startTime)->addMinutes(15)->format('H:i');

            $isDistributionStarted = FoodDistribution::where('center_id', $student->center_id)
                ->where('meal_type', $type)
                ->whereDate('distributed_at', $today)
                ->exists();

            $isLateExpired = now()->gt(Carbon::today()->setTimeFromTimeString($lateDeadline));
            $isAbsentExpired = now()->gt(Carbon::today()->setTimeFromTimeString($absentDeadline));

            $meals[] = (object) [
                'type' => $type,
                'label' => $this->getMealLabel($type),
                'time' => $startTime,
                'status' => $report ? $report->status : 'normal',
                'can_edit' => !$isDistributionStarted,
                'is_late_expired' => $isLateExpired,
                'is_absent_expired' => $isAbsentExpired,
                'lock_reason' => $isDistributionStarted ? 'بدأ التوزيع' : null,
            ];
        }

        return $meals;
    }

    private function getMealLabel($type)
    {
        return match ($type) {
            'breakfast' => 'الفطور',
            'lunch' => 'الغداء',
            'dinner' => 'العشاء',
            default => $type
        };
    }
}
