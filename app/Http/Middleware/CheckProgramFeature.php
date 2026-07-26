<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Student;
use Illuminate\Http\Request;

class CheckProgramFeature
{
    /**
     * التحقق من أن الطالب المحدد ينتمي لبرنامج يدعم الميزة المطلوبة.
     *
     * الاستخدام في routes:
     *   ->middleware('program.feature:violations')
     *   ->middleware('program.feature:attendance')
     *   ->middleware('program.feature:activities')
     *   ->middleware('program.feature:evaluation')
     */
    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        // جلب الطالب من route parameter
        $student = $request->route('student');

        if (! $student instanceof Student) {
            $student = Student::with('program')->find($student);
        } elseif (! $student->relationLoaded('program')) {
            $student->load('program');
        }

        if ($student && ! $student->allows($feature)) {
            $featureNames = [
                'activities'   => 'الأنشطة والفعاليات',
                'attendance'   => 'تسجيل الحضور والغياب',
                'violations'   => 'المخالفات والعقوبات',
                'evaluation'   => 'التقييمات والدرجات',
                'quran_circle' => 'حلقات القرآن',
                'leaves'       => 'الإجازات',
            ];

            $featureName = $featureNames[$feature] ?? $feature;
            $message = "ميزة \"{$featureName}\" غير متاحة لطلاب برنامج \"{$student->program?->name}\".";

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
