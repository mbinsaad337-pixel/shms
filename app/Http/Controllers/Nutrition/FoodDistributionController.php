<?php

namespace App\Http\Controllers\Nutrition;

use App\Http\Controllers\Controller;
use App\Models\FoodDistribution;
use App\Models\FoodSubscription;
use App\Models\FoodQrGroup;
use App\Models\FoodQrGroupMember;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class FoodDistributionController extends Controller
{
    /** Main scan interface */
    public function scan()
    {
        return view('nutrition.distributions.scan');
    }

    /** API: validate & process QR scan */
    public function processScan(Request $request)
    {
        Log::info('FoodDistribution::processScan', ['qr_code' => substr($request->qr_code, 0, 30)]);
        $request->validate(['qr_code' => 'required|string']);

        $qrCode = trim($request->qr_code);
        // Remove BOM and invisible characters
        $qrCode = preg_replace('/[\x00-\x1F\x7F\xC2\xA0]/u', '', $qrCode);
        $qrCode = trim($qrCode);
        $centerId = auth()->user()->center_id;
        $mealType = $this->getCurrentMealType();

        // --- Individual QR ---
        if (str_starts_with($qrCode, 'FOOD_SUB_')) {
            $subscription = FoodSubscription::where('qr_code', $qrCode)
                ->where('center_id', $centerId)
                ->with('student')
                ->first();

            if (!$subscription) {
                return response()->json(['success' => false, 'message' => 'QR غير صالح أو غير مسجل في هذا المركز.'], 404);
            }

            $subscription->checkAndAutoSuspend();
            $subscription->refresh();

            if ($subscription->status === 'suspended') {
                return response()->json(['success' => false, 'message' => 'الطالب موقوف من التغذية.', 'status' => 'suspended'], 403);
            }

            if ($subscription->status === 'expired') {
                return response()->json(['success' => false, 'message' => 'انتهت صلاحية الاشتراك.', 'status' => 'expired'], 403);
            }

            // --- Handoff to Attendance Check ---
            $attendance = \App\Models\FoodAttendanceReport::where('student_id', $subscription->student_id)
                ->where('meal_date', today())
                ->where('meal_type', $mealType)
                ->first();

            if ($attendance && $attendance->status === 'absent') {
                return response()->json(['success' => false, 'message' => 'الطالب مسجل كـ "غائب" لهذه الوجبة.'], 403);
            }

            // Duplicate check
            $alreadyDistributed = FoodDistribution::where('student_id', $subscription->student_id)
                ->where('meal_type', $mealType)
                ->where('distribution_type', '!=', 'extra')
                ->whereDate('distributed_at', today())
                ->exists();

            return response()->json([
                'success' => true,
                'type' => 'individual',
                'meal_type' => $this->getMealLabel($mealType),
                'meal_key' => $mealType,
                'students' => [
                    [
                        'id' => $subscription->student->id,
                        'name' => $subscription->student->name_ar,
                        'subscription_id' => $subscription->id,
                        'attendance_status' => $attendance?->status ?? 'normal',
                        'status' => $alreadyDistributed ? 'already_received' : 'ready',
                    ]
                ],
            ]);
        }

        // --- Group QR ---
        if (str_starts_with($qrCode, 'GRP_')) {
            $token = substr($qrCode, 4);
            $group = FoodQrGroup::where('qr_token', $token)
                ->where('center_id', $centerId)
                ->with('members.student')
                ->first();

            if (!$group) {
                return response()->json(['success' => false, 'message' => 'QR المجمع غير صالح.'], 404);
            }

            if (!$group->isValid()) {
                return response()->json(['success' => false, 'message' => 'QR المجمع منتهي الصلاحية أو استُخدم مسبقاً.'], 403);
            }

            $memberData = [];
            $memberStudentIds = $group->members->pluck('student_id')->toArray();

            $existingDistributions = FoodDistribution::whereIn('student_id', $memberStudentIds)
                ->where('meal_type', $mealType)
                ->where('distribution_type', '!=', 'extra')
                ->whereDate('distributed_at', today())
                ->pluck('student_id')
                ->flip();

            $attendanceReports = \App\Models\FoodAttendanceReport::whereIn('student_id', $memberStudentIds)
                ->where('meal_date', today())
                ->where('meal_type', $mealType)
                ->keyBy('student_id');

            foreach ($group->members as $member) {
                $sub = $member->subscription;
                if ($sub) {
                    $sub->checkAndAutoSuspend();
                    $sub->refresh();
                }

                $alreadyDist = $existingDistributions->has($member->student_id);

                $attendance = $attendanceReports->get($member->student_id);

                $status = 'ready';
                if ($alreadyDist) {
                    $status = 'already_received';
                } elseif ($attendance && $attendance->status === 'absent') {
                    $status = 'absent';
                } elseif (!$sub) {
                    $status = 'no_subscription';
                } elseif ($sub->status === 'suspended') {
                    $status = 'suspended';
                } elseif ($sub->status !== 'active') {
                    $status = $sub->status;
                }

                $memberData[] = [
                    'id' => $member->student->id,
                    'name' => $member->student->name_ar,
                    'subscription_id' => $member->subscription_id,
                    'attendance_status' => $attendance?->status ?? 'normal',
                    'status' => $status,
                ];
            }

            return response()->json([
                'success' => true,
                'type' => 'group',
                'group_id' => $group->id,
                'meal_type' => $this->getMealLabel($mealType),
                'meal_key' => $mealType,
                'students' => $memberData,
                'total_members' => $group->members_count,
                'creator' => $group->creatorStudent->name_ar ?? 'غير معروف',
            ]);
        }

        // --- Student QR Group ---
        $studentGroupToken = null;

        // Clean QR code: strip any URL prefix, extract only the token part
        $cleanQr = trim($qrCode);

        // Handle full URL format: http(s)://any-domain/any-path/student-qr-groups/scan/TOKEN
        if (str_contains($cleanQr, 'student-qr-groups/scan/')) {
            $parts = explode('student-qr-groups/scan/', $cleanQr);
            $studentGroupToken = trim(end($parts));
        } else {
            // Try as raw token directly
            $exists = \App\Models\StudentQrGroup::where('group_token', $cleanQr)->exists();
            if ($exists) {
                $studentGroupToken = $cleanQr;
            }
        }

        if ($studentGroupToken) {
            $group = \App\Models\StudentQrGroup::where('group_token', $studentGroupToken)
                ->with(['students.activeFoodSubscription', 'primaryStudent.activeFoodSubscription'])
                ->first();

            if (!$group) {
                Log::warning('StudentQrGroup not found', [
                    'raw_qr' => $request->qr_code,
                    'cleaned_qr' => $cleanQr,
                    'token_used' => $studentGroupToken,
                    'token_hex' => bin2hex($studentGroupToken),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'رمز QR المجمع غير موجود.',
                    'debug_token' => $studentGroupToken,
                    'debug_raw' => substr($request->qr_code, 0, 100),
                ], 404);
            }

            if ($group->expires_at && $group->expires_at->isPast()) {
                return response()->json(['success' => false, 'message' => 'انتهت صلاحية رمز QR المجمع.', 'status' => 'expired'], 403);
            }

            if ($group->status !== 'active') {
                return response()->json(['success' => false, 'message' => 'رمز QR المجمع غير فعال.'], 403);
            }

            $allStudents = collect([$group->primaryStudent])->merge($group->students);
            $memberData = [];
            $allStudentIds = $allStudents->filter()->pluck('id')->toArray();

            $existingDistributions2 = FoodDistribution::whereIn('student_id', $allStudentIds)
                ->where('meal_type', $mealType)
                ->where('distribution_type', '!=', 'extra')
                ->whereDate('distributed_at', today())
                ->pluck('student_id')
                ->flip();

            $attendanceReports2 = \App\Models\FoodAttendanceReport::whereIn('student_id', $allStudentIds)
                ->where('meal_date', today())
                ->where('meal_type', $mealType)
                ->keyBy('student_id');

            foreach ($allStudents as $st) {
                if (!$st)
                    continue;

                $sub = $st->activeFoodSubscription;
                if ($sub) {
                    $sub->checkAndAutoSuspend();
                    $sub->refresh();
                }

                $alreadyDist = $existingDistributions2->has($st->id);

                $attendance = $attendanceReports2->get($st->id);

                $status = 'ready';
                if ($alreadyDist) {
                    $status = 'already_received';
                } elseif ($attendance && $attendance->status === 'absent') {
                    $status = 'absent';
                } elseif (!$sub) {
                    $status = 'no_subscription';
                } elseif ($sub->status === 'suspended') {
                    $status = 'suspended';
                } elseif ($sub->status !== 'active') {
                    $status = $sub->status;
                }

                $memberData[] = [
                    'id' => $st->id,
                    'name' => $st->name_ar,
                    'subscription_id' => $sub?->id,
                    'attendance_status' => $attendance?->status ?? 'normal',
                    'status' => $status,
                ];
            }

            return response()->json([
                'success' => true,
                'type' => 'student_group',
                'group_id' => $group->id,
                'meal_type' => $this->getMealLabel($mealType),
                'meal_key' => $mealType,
                'students' => $memberData,
                'total_members' => count($memberData),
                'creator' => $group->primaryStudent->name_ar ?? 'غير معروف',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'QR غير معروف.'], 400);
    }

    /** API: record distribution after confirmation */
    public function distribute(Request $request)
    {
        Log::info('FoodDistribution::distribute', [
            'student_count' => count($request->students),
            'meal_type' => $request->meal_type,
            'type' => $request->type,
        ]);

        $request->validate([
            'students' => 'required|array',
            'students.*.student_id' => 'required|exists:students,id',
            'students.*.subscription_id' => 'nullable|exists:food_subscriptions,id',
            'meal_type' => 'required|string',
            'dish_number' => 'nullable|string',
            'type' => 'required|in:individual,group,extra',
            'group_id' => 'nullable|exists:food_qr_groups,id',
            'student_qr_group_id' => 'nullable|exists:student_qr_groups,id',
        ]);

        $centerId = auth()->user()->center_id;
        $distributed = [];
        $skipped = [];
        $groupName = null;
        $primaryStudentId = $request->students[0]['student_id']; // For group display or first student

        // If it's a group, determine group name
        if ($request->group_id) {
            $g = FoodQrGroup::with('creatorStudent')->find($request->group_id);
            $groupName = "مجموعة: " . ($g->creatorStudent->name_ar ?? 'مجمع');
        } elseif ($request->student_qr_group_id) {
            $g = \App\Models\StudentQrGroup::with('primaryStudent')->find($request->student_qr_group_id);
            $groupName = "مجموعة: " . ($g->primaryStudent->name_ar ?? 'مجمع');
        }

        if ($groupName) {
            // Group Mode: Record one MASTER log and then individual logs (optional or hidden?)
            // Let's record INDIVIDUAL logs for data integrity (eligibility checks) BUT 
            // only the first one will be marked with the group name for the table display? 
            // Better: Record one record per group in table, but keep individual distributions for tracking.

            // To keep things simple and fix the grouping bug: 
            // We'll record a SINGLE record for the GROUP in the table, 
            // AND we'll also record individual records for eligibility checks next time.

            foreach ($request->students as $s) {
                // Individual tracking (for future scan checks)
                FoodDistribution::create([
                    'center_id' => $centerId,
                    'student_id' => $s['student_id'],
                    'subscription_id' => $s['subscription_id'] ?? null,
                    'qr_group_id' => $request->group_id ?? null,
                    'student_qr_group_id' => $request->student_qr_group_id ?? null,
                    'meal_type' => $request->meal_type,
                    'distribution_type' => $request->type,
                    'dish_number' => $request->dish_number,
                    'scan_type' => 'group_qr',
                    'distributed_by' => auth()->id(),
                    'distributed_at' => now(),
                    'notes' => 'group_member', // Internal note
                ]);
                $distributed[] = $s['student_id'];
            }

            // Create the SUMMARY record for the table
            FoodDistribution::create([
                'center_id' => $centerId,
                'student_id' => $primaryStudentId,
                'qr_group_id' => $request->group_id ?? null,
                'student_qr_group_id' => $request->student_qr_group_id ?? null,
                'group_name' => $groupName,
                'group_members_count' => count($request->students),
                'meal_type' => $request->meal_type,
                'distribution_type' => $request->type,
                'scan_type' => 'group_qr',
                'distributed_by' => auth()->id(),
                'distributed_at' => now(),
            ]);

        } else {
            // Individual Mode
            foreach ($request->students as $s) {
                // Skip already distributed (except extra)
                if ($request->type !== 'extra') {
                    $exists = FoodDistribution::where('student_id', $s['student_id'])
                        ->where('meal_type', $request->meal_type)
                        ->where('distribution_type', '!=', 'extra')
                        ->whereDate('distributed_at', today())
                        ->exists();

                    if ($exists) {
                        $skipped[] = $s['student_id'];
                        continue;
                    }
                }

                FoodDistribution::create([
                    'center_id' => $centerId,
                    'student_id' => $s['student_id'],
                    'subscription_id' => $s['subscription_id'] ?? null,
                    'meal_type' => $request->meal_type,
                    'distribution_type' => $request->type,
                    'dish_number' => $request->dish_number,
                    'scan_type' => 'individual_qr',
                    'distributed_by' => auth()->id(),
                    'distributed_at' => now(),
                ]);
                $distributed[] = $s['student_id'];
            }
        }

        // Mark group as used ONLY if it's an "extra" distribution
        if ($request->type === 'extra') {
            if ($request->group_id) {
                FoodQrGroup::find($request->group_id)?->update(['is_used' => true]);
            }
            if ($request->student_qr_group_id) {
                \App\Models\StudentQrGroup::find($request->student_qr_group_id)?->update(['status' => 'used']);
            }
        }

        return response()->json([
            'success' => true,
            'distributed' => count($distributed),
            'skipped' => count($skipped),
            'message' => 'تمت عملية التوزيع بنجاح.',
        ]);
    }

    /** View distribution log */
    public function index(Request $request)
    {
        $centerId = auth()->user()->center_id;
        $query = FoodDistribution::with(['student', 'distributor', 'qrGroup.creatorStudent', 'studentQrGroup.primaryStudent'])
            ->where('center_id', $centerId);

        if ($request->date)
            $query->whereDate('distributed_at', $request->date);
        else
            $query->whereDate('distributed_at', today());

        if ($request->meal_type)
            $query->where('meal_type', $request->meal_type);
        if ($request->type)
            $query->where('distribution_type', $request->type);

        // Only show individual distributions or group summary records (where group_name is set)
        $query->whereRaw("(notes IS NULL OR notes != 'group_member')");

        $distributions = $query->latest('distributed_at')->paginate(30);

        // Map through to add member count for individual rows (visual only) - REMOVED as group_members_count is now stored
        // $distributions->getCollection()->transform(function ($item) {
        //     $isGroup = $item->qr_group_id || $item->student_qr_group_id;
        //     if ($isGroup) {
        //         $item->group_members_count = FoodDistribution::where('meal_type', $item->meal_type)
        //             ->whereDate('distributed_at', $item->distributed_at->toDateString())
        //             ->where(function ($q) use ($item) {
        //                 if ($item->qr_group_id)
        //                     $q->where('qr_group_id', $item->qr_group_id);
        //                 else
        //                     $q->where('student_qr_group_id', $item->student_qr_group_id);
        //             })->count();
        //     }
        //     return $item;
        // });

        $todayDate = today();
        $todayStats = [
            // Total students who got a regular meal (individual or group members)
            'total_meals' => FoodDistribution::where('center_id', $centerId)
                ->whereDate('distributed_at', $todayDate)
                ->where('distribution_type', '!=', 'extra')
                ->whereNull('group_name') // Don't count the summary record
                ->count(),

            // Just individual student distributions
            'individual' => FoodDistribution::where('center_id', $centerId)
                ->whereDate('distributed_at', $todayDate)
                ->where('distribution_type', 'individual')
                ->count(),

            // Distinct group distribution totals (how many groups received food)
            'group_count' => FoodDistribution::where('center_id', $centerId)
                ->whereDate('distributed_at', $todayDate)
                ->where('distribution_type', 'group')
                ->whereNotNull('group_name') // Count summary records
                ->count(),

            // Distinct extra group distributions
            'extra_groups' => FoodDistribution::where('center_id', $centerId)
                ->whereDate('distributed_at', $todayDate)
                ->where('distribution_type', 'extra')
                ->whereNotNull('group_name') // Count summary records
                ->count(),
        ];

        return view('nutrition.distributions.index', compact('distributions', 'todayStats'));
    }

    public function details(FoodDistribution $distribution)
    {
        $centerId = auth()->user()->center_id;
        if ($distribution->center_id !== $centerId)
            abort(403);

        $query = FoodDistribution::with('student')
            ->where('meal_type', $distribution->meal_type)
            ->whereDate('distributed_at', $distribution->distributed_at->toDateString());

        if ($distribution->qr_group_id) {
            $query->where('qr_group_id', $distribution->qr_group_id);
        } elseif ($distribution->student_qr_group_id) {
            $query->where('student_qr_group_id', $distribution->student_qr_group_id);
        } else {
            return response()->json(['students' => [$distribution->student]]);
        }

        $students = $query->where('notes', 'group_member')->get()->pluck('student')->unique('id')->values();
        return response()->json(['students' => $students]);
    }

    public function destroy(FoodDistribution $distribution)
    {
        $centerId = auth()->user()->center_id;
        if ($distribution->center_id !== $centerId)
            abort(403);

        // If it's a group distribution sumary, delete all related member records too
        if ($distribution->qr_group_id || $distribution->student_qr_group_id) {
            $query = FoodDistribution::where('meal_type', $distribution->meal_type)
                ->whereDate('distributed_at', $distribution->distributed_at->toDateString());

            if ($distribution->qr_group_id) {
                $query->where('qr_group_id', $distribution->qr_group_id);
            } else {
                $query->where('student_qr_group_id', $distribution->student_qr_group_id);
            }

            $query->delete();
        } else {
            // Individual
            $distribution->delete();
        }

        return back()->with('success', 'تم حذف سجل التوزيع بنجاح.');
    }

    private function getCurrentMealType(): string
    {
        $hour = (int) date('H');
        if ($hour < 11)
            return 'breakfast';
        if ($hour < 17)
            return 'lunch';
        return 'dinner';
    }

    private function getMealLabel(string $type): string
    {
        return match ($type) {
            'breakfast' => 'فطور',
            'lunch' => 'غداء',
            'dinner' => 'عشاء',
            default => $type,
        };
    }
}
