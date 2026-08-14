<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return redirect()->route('students.show', $user->student);
        }
        
        if (!$user->can('view-students')) {
            abort(403);
        }

        $query = Student::query()
            ->where('is_graduate', false)
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id));

        // Search
        if ($request->search) {
            $query->where(function ($sub) use ($request) {
                $sub->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('student_number', 'like', '%' . $request->search . '%')
                    ->orWhere('national_id', 'like', '%' . $request->search . '%');
            });
        }

        // Housing Filter (for Super Admin)
        if ($user->hasRole('super-admin') && $request->filled('housing')) {
            $query->where('center_id', $request->housing);
        }

        // Generic Filters
        $filters = ['major', 'university', 'college', 'academic_level', 'status', 'nationality'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $students = $query->with(['center', 'user', 'program'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Extract values for filters
        $filterOptions = [];
        foreach ($filters as $filter) {
            $filterOptions[$filter . 's'] = Student::query()
                ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
                ->whereNotNull($filter)
                ->where($filter, '!=', '')
                ->distinct()
                ->pluck($filter);
        }

        // Add Centers for Housing Filter
        if ($user->hasRole('super-admin')) {
            $filterOptions['centers'] = Center::all();
        }

        // Programs Filter Options
        $filterOptions['programs'] = \App\Models\Program::active()->get();

        return view('students.index', array_merge(compact('students'), $filterOptions));
    }

    public function create()
    {
        $programs = \App\Models\Program::active()->get();
        return view('students.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar'        => 'required|string|max:255',
            'email'          => 'nullable|email|unique:users,email',
            'username'       => 'required|string|unique:users,username',
            'temp_password'  => 'required|string|min:6',
            'phone'          => 'required|string|max:20',
            'national_id'    => 'nullable|string|unique:students,national_id',
            'student_number' => 'nullable|string|unique:students,student_number',
            'program_id'     => 'required|exists:programs,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $center_id = auth()->user()->center_id ?? Center::first()->id;

            // Generate placeholder email if not provided
            $email = $validated['email'] ?? $validated['username'] . '@student.shms.local';

            // Create User account for student
            $user = User::create([
                'name' => $validated['name_ar'],
                'email' => $email,
                'username' => $validated['username'],
                'password' => Hash::make($validated['temp_password']),
                'temp_password' => $validated['temp_password'],
                'center_id' => $center_id,
                'is_active' => true,
                'must_change_password' => true,
                'profile_completed' => false,
            ]);

            $user->assignRole('student');

            // Create Student record
            $student = Student::create([
                'user_id'        => $user->id,
                'center_id'      => $center_id,
                'program_id'     => $validated['program_id'],
                'name_ar'        => $validated['name_ar'],
                'phone'          => $validated['phone'],
                'email'          => $validated['email'] ?? null,
                'national_id'    => $validated['national_id'] ?? null,
                'student_number' => $validated['student_number'] ?? null,
                'annual_fees'    => $request->annual_fees ?? 0,
                'barcode'        => 'ST-' . strtoupper(Str::random(8)),
                'status'         => 'registered',
            ]);

            // Build WhatsApp welcome message
            $centerName = auth()->user()->center->name ?? 'السكن الطلابي';
            $loginUrl = url('/login');
            $whatsappMessage = "السلام عليكم ورحمة الله وبركاته\n\n"
                . "🎉 *مرحباً بك يا {$validated['name_ar']}*\n"
                . "يسعدنا إبلاغك بقبولك في *{$centerName}*.\n\n"
                . "📋 *بيانات الدخول الخاصة بك:*\n"
                . "👤 اسم المستخدم: `{$validated['username']}`\n"
                . "🔑 كلمة المرور: `{$validated['temp_password']}`\n\n"
                . "🔗 رابط الدخول للنظام:\n{$loginUrl}\n\n"
                . "⚠️ يرجى تغيير كلمة المرور عند أول تسجيل دخول وإكمال بياناتك الشخصية.\n\n"
                . "نتمنى لك إقامة طيبة 🏠";

            // Format phone for WhatsApp
            $whatsappService = app(\App\Services\WhatsAppService::class);
            $phone = $whatsappService->normalizePhone($validated['phone']);

            $whatsappUrl = 'https://wa.me/' . $phone . '?text=' . urlencode($whatsappMessage);

            return redirect()->route('students.index')
                ->with('success', 'تم تسجيل الطالب بنجاح.')
                ->with('whatsapp_url', $whatsappUrl);
        });
    }

    public function show(Student $student)
    {
        $user = auth()->user();
        
        // If student, check if it's their own record
        if ($user->hasRole('student')) {
            if ($student->user_id !== $user->id) {
                abort(403, 'لا يمكنك عرض ملفات طلاب آخرين.');
            }
        } else {
            // Check for view-students permission (though middleware handles this usually)
            if (!$user->can('view-students')) {
                abort(403);
            }
        }

        // Load relationships
        $student->load([
            'center',
            'program',
            'roomAssignments.room',
            'violations.recordedBy',
            'penalties.appliedBy',
            'leaves.approvedBy',
            'user',
            'mealSubscription'
        ]);

        return view('students.show', compact('student'));
    }

    public function approveProfile(Student $student)
    {
        $user = auth()->user();
        if ($student->center_id !== $user->center_id && !$user->hasRole('super-admin') && !$user->hasRole('executive-manager')) {
            abort(403);
        }

        $student->update(['is_profile_approved' => true]);

        return back()->with('success', 'تم اعتماد بيانات الطالب بنجاح.');
    }
    public function edit(Student $student)
    {
        $programs = \App\Models\Program::active()->get();
        return view('students.edit', compact('student', 'programs'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'username' => 'nullable|string|unique:users,username,' . $student->user_id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email,' . $student->user_id,
            'national_id' => 'nullable|string|unique:students,national_id,' . $student->id,
            'student_number' => 'nullable|string|unique:students,student_number,' . $student->id,
            'status' => 'nullable|in:registered,residing,left,graduated,suspended',
            'annual_fees' => 'nullable|numeric|min:0',
            'date_of_birth' => 'nullable|date',
            'id_card_date' => 'nullable|date',
            'enrollment_date' => 'nullable|date',
            'family_males' => 'nullable|integer|min:0',
            'family_females' => 'nullable|integer|min:0',
            'family_avg_income' => 'nullable|numeric|min:0',
            'dependents_count' => 'nullable|integer|min:0',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $studentData = $request->only([
            'name_ar', 'name_en', 'surname', 'national_id', 'phone', 'nationality', 'date_of_birth', 
            'place_of_birth', 'blood_type', 'marital_status', 'health_status',
            'id_card_number', 'id_card_source', 'id_card_date',
            'home_phone', 'governorate', 'district', 'village', 'permanent_address',
            'status', 'student_number', 'university', 'college', 'major', 
            'academic_level', 'enrollment_date', 'expected_graduation', 'annual_fees',
            'last_certificate', 'graduated_school', 'last_cert_major', 'graduation_year', 'last_cert_grade',
            'guardian_name', 'guardian_relation', 'guardian_phone', 'guardian_job', 'guardian_education',
            'family_males', 'family_females', 'family_avg_income', 'dependents_count',
            'emergency_name', 'emergency_relation', 'emergency_phone', 'skills', 'program_id'
        ]);
        $student->update($studentData);
        
        $userData = [];
        if ($request->has('name_ar')) $userData['name'] = $request->name_ar;
        if ($request->has('email')) $userData['email'] = $request->email;
        if ($request->has('username')) $userData['username'] = $request->username;

        if ($request->has('workers')) {
            $familyWorkers = [];
            foreach ($request->input('workers', []) as $w) {
                if (!empty($w['name'])) {
                    $familyWorkers[] = [
                        'name'         => $w['name'] ?? '',
                        'job'          => $w['job'] ?? '',
                        'organization' => $w['organization'] ?? '',
                        'phone'        => $w['phone'] ?? '',
                    ];
                }
            }
            $student->update(['family_workers' => $familyWorkers ?: null]);
        }

        if (!empty($request->password)) {
            $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
            $userData['temp_password'] = $request->password;
        }

        if (!empty($userData)) {
            $student->user->update($userData);
        }

        return redirect()->route('students.show', $student)->with('success', 'تم تحديث بيانات الطالب بنجاح.');
    }

    public function exportPdf(Student $student, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        
        // If student, check if it's their own record
        if ($user->hasRole('student')) {
            if ($student->user_id !== $user->id) {
                abort(403, 'لا يمكنك تصدير ملفات طلاب آخرين.');
            }
        } elseif (!$user->can('view-students')) {
            abort(403);
        }

        $student->load([
            'center',
            'program',
            'user',
            'roomAssignments.room',
            'activeRoomAssignment.room',
            'mealSubscription',
            'foodSubscriptions',
            'violations.recordedBy',
            'penalties.appliedBy',
            'leaves.approvedBy',
            'absences',
            'grades',
            'achievements',
            'quranCircles',
            'circleAttendances',
            'vouchers' => fn($q) => $q->latest(),
        ]);

        // تحويل الصور إلى base64 لتضمينها في PDF
        $photoBase64 = $this->imageToBase64($student->photo);
        $idCardFileBase64 = $this->imageToBase64($student->id_card_file);
        $certificateFileBase64 = $this->imageToBase64($student->certificate_file);
        $universityCardFileBase64 = $this->imageToBase64($student->university_card_file);

        // توليد باركود QR كـ base64
        $barcodeBase64 = null;
        if ($student->barcode) {
            try {
                $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(150)
                    ->generate($student->barcode);
                $barcodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
            } catch (\Exception $e) {
                $barcodeBase64 = null;
            }
        }

        // حساب الإحصائيات المالية
        $totalPaid = $student->vouchers()->where('type', 'receipt')->where('status', 'approved')->sum('amount');
        $remainingFees = max(0, (float) $student->annual_fees - $totalPaid);

        $filename = 'student_profile_' . $student->student_number . '.pdf';

        return $pdfService->stream('pdf.reports.student-profile', [
            'student' => $student,
            'photoBase64' => $photoBase64,
            'idCardFileBase64' => $idCardFileBase64,
            'certificateFileBase64' => $certificateFileBase64,
            'universityCardFileBase64' => $universityCardFileBase64,
            'barcodeBase64' => $barcodeBase64,
            'totalPaid' => $totalPaid,
            'remainingFees' => $remainingFees,
        ], 'ملف الطالب الكامل', $filename, 'portrait');
    }

    /**
     * تحويل مسار الصورة إلى base64 لتضمينها في PDF
     */
    private function imageToBase64(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return null;
        }

        $mimeType = mime_content_type($fullPath);
        $data = file_get_contents($fullPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($data);
    }

    public function exportListPdf(Request $request, \App\Services\PdfService $pdfService)
    {
        $user = auth()->user();
        $query = Student::query()
            ->when($request->has('is_graduate'), function($q) use ($request) {
                return $q->where('is_graduate', $request->is_graduate);
            }, function($q) {
                return $q->where('is_graduate', false);
            })
            ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id));

        // Apply filters
        if ($request->search) {
            $query->where(function ($sub) use ($request) {
                $sub->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('student_number', 'like', '%' . $request->search . '%')
                    ->orWhere('national_id', 'like', '%' . $request->search . '%');
            });
        }

        // Housing Filter (for Super Admin)
        if ($user->hasRole('super-admin') && $request->filled('housing')) {
            $query->where('center_id', $request->housing);
        }

        $filters = ['major', 'university', 'college', 'academic_level', 'status', 'nationality'];
        $appliedFilters = [];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
                $appliedFilters[$filter] = $request->input($filter);
            }
        }

        $students = $query->with(['center'])->latest()->get();

        $isGraduate = $request->boolean('is_graduate');
        $reportTitle = $isGraduate ? 'تقرير قائمة الطلاب الخريجين' : 'تقرير قائمة الطلاب';
        $fileName = ($isGraduate ? 'alumni_list_' : 'students_list_') . now()->format('Y-m-d') . '.pdf';

        return $pdfService->stream('pdf.reports.students', [
            'data' => $students,
            'isGraduate' => $isGraduate,
        ], $reportTitle, $fileName, 'landscape', $appliedFilters);
    }

    public function markAsGraduate(Student $student)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        return DB::transaction(function () use ($student) {
            // 1. Mark as graduate
            $student->update([
                'is_graduate' => true,
                'status' => 'graduated',
                'graduation_year' => now()->year
            ]);

            $notices = [];

            // 2. Vacate Room if assigned
            $activeAssignment = $student->activeRoomAssignment;
            if ($activeAssignment) {
                $roomNumber = $activeAssignment->room->room_number ?? 'غير معروف';
                $activeAssignment->update([
                    'released_at' => now(),
                    'release_reason' => 'تخرج'
                ]);
                $notices[] = "تم إخلاء الطالب من الغرفة رقم ({$roomNumber}) تلقائياً.";
            }

            // 3. Cancel Active Nutrition Subscriptions
            $activeFoodSub = $student->activeFoodSubscription;
            $pendingBalance = 0;
            if ($activeFoodSub) {
                $pendingBalance = $activeFoodSub->total_due - $activeFoodSub->total_paid;
                $activeFoodSub->update(['status' => 'expired']);
                $notices[] = "تم إغلاق اشتراك التغذية النشط.";
            }

            // 4. Financial Review Summary
            // Checking all food subscriptions for balances
            $totalRemaining = $student->foodSubscriptions()->get()->sum(function($sub) {
                return $sub->total_due - $sub->total_paid;
            });

            if ($totalRemaining > 0) {
                $notices[] = "تنبيه مالي: يوجد مبلع مستحق بذمة الطالب قدره ({$totalRemaining}) من اشتراكات التغذية.";
            } elseif ($totalRemaining < 0) {
                $notices[] = "تنبيه مالي: للطالب مبلغ فائض (دائن) قدره (" . abs($totalRemaining) . ").";
            } else {
                $notices[] = "المراجعة المالية: ذمة الطالب المالية في وحدة التغذية مسواة.";
                
            }

            $message = "تم نقل الطالب إلى قائمة الخريجين بنجاح. \n" . implode("\n", $notices);
            return redirect()->route('students.index')->with('success', $message);
        });
    }

    public function alumni(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        $query = Student::query()
            ->where('is_graduate', true);

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        } elseif ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        // Search
        if ($request->search) {
            $query->where(function ($sub) use ($request) {
                $sub->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('name_en', 'like', '%' . $request->search . '%')
                    ->orWhere('student_number', 'like', '%' . $request->search . '%')
                    ->orWhere('national_id', 'like', '%' . $request->search . '%')
                    ->orWhere('job_title', 'like', '%' . $request->search . '%');
            });
        }

        // Generic Filters
        $filters = ['major', 'university', 'college', 'academic_level', 'nationality', 'graduation_year', 'job_title'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }

        $students = $query->with(['center', 'user', 'graduationAttachments'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Extract values for filters
        $filterOptions = [];
        foreach ($filters as $filter) {
            $filterOptions[$filter . 's'] = Student::query()
                ->where('is_graduate', true)
                ->when($user->center_id, fn($q) => $q->where('center_id', $user->center_id))
                ->whereNotNull($filter)
                ->where($filter, '!=', '')
                ->distinct()
                ->pluck($filter);
        }

        if (!$user->center_id) {
            $filterOptions['centers'] = \App\Models\Center::all();
        }

        return view('students.alumni', array_merge(compact('students'), $filterOptions));
    }

    public function restoreFromGraduate(Student $student)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        $student->update([
            'is_graduate' => false,
            'status' => 'residing' // Or whatever the active status should be
        ]);

        return redirect()->route('students.alumni')->with('success', 'تم إعادة الطالب إلى قائمة الطلاب النشطين بنجاح.');
    }

    public function toggleEditPermission(Student $student)
    {
        $student->update([
            'can_edit_profile' => !$student->can_edit_profile
        ]);

        $message = $student->can_edit_profile ? 'تم منح الطالب صلاحية تعديل بياناته.' : 'تم سحب صلاحية التعديل من الطالب.';
        return back()->with('success', $message);
    }

    public function toggleCircleTeacher(Student $student)
    {
        $user = auth()->user();
        if (!$user->can('manage-users') && !$user->hasRole('center-manager') && !$user->hasRole('housing-manager') && !$user->hasRole('supervisor')) {
            abort(403);
        }

        $studentUser = $student->user;
        if ($studentUser->hasRole('circle-teacher')) {
            $studentUser->removeRole('circle-teacher');
            $message = 'تم سحب صلاحية "مدرس حلقة" من الطالب.';
        } else {
            $studentUser->assignRole('circle-teacher');
            $message = 'تم منح الطالب صلاحية "مدرس حلقة" بنجاح.';
        }

        return back()->with('success', $message);
    }

    public function toggleActivityAssistant(Student $student)
    {
        $user = auth()->user();
        if (!$user->can('manage-users') && !$user->hasRole('center-manager') && !$user->hasRole('social-manager')) {
            abort(403);
        }

        $studentUser = $student->user;
        if ($studentUser->hasRole('activity-assistant')) {
            $studentUser->removeRole('activity-assistant');
            $message = 'تم سحب صلاحية "مساعد أنشطة" من الطالب.';
        } else {
            // Ensure the role exists first (best practice)
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'activity-assistant', 'guard_name' => 'web'])
                ->syncPermissions(['view-activities', 'register-activities']);
            
            $studentUser->assignRole('activity-assistant');
            $message = 'تم منح الطالب صلاحية "مساعد أنشطة" بنجاح، يمكنه الآن تسجيل حضور الطلاب في الفعاليات.';
        }

        return back()->with('success', $message);
    }

    public function applyAnnualFees(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('center-manager') && !$user->can('manage-students')) {
            abort(403, 'غير مصرح لك للقيام بهذه العملية');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'program_id' => 'required',
        ]);

        $query = Student::query()
            ->where('is_graduate', false)
            ->where('status', '!=', 'left');

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        }

        if ($validated['program_id'] !== 'all') {
            $query->where('program_id', $validated['program_id']);
            $programName = \App\Models\Program::find($validated['program_id'])->name ?? 'المحدد';
            $msgTarget = "طلاب البرنامج " . $programName;
        } else {
            $msgTarget = "جميع الطلاب";
        }

        $query->update(['annual_fees' => $validated['amount']]);

        return redirect()->route('students.index')->with('success', 'تم تعميم الرسوم السنوية (' . number_format($validated['amount'], 2) . ') على ' . $msgTarget . ' بنجاح.');
    }

    public function destroy(Student $student)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403, 'غير مصرح لك بحذف بيانات الطلاب.');
        }

        // Deactivate user account
        $student->user?->update(['is_active' => false]);

        // Soft delete the student record
        $student->delete();

        return redirect()->route('students.index')->with('success', 'تم حذف بيانات الطالب بنجاح.');
    }
}
