<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return redirect()->route('dashboard');
        }
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('student')) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'تم تحديث الملف الشخصي بنجاح.');
    }

    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متوافق.',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }

    public function showCompleteProfile()
    {
        $user = auth()->user();

        if (!$user->hasRole('student')) {
            return redirect()->route('dashboard');
        }

        $student = $user->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'سجل الطالب غير موجود.');
        }

        return view('profile.complete-profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'سجل الطالب غير موجود.');
        }

        $validated = $request->validate([
            // Personal
            'name_ar'                => 'required|string|max:255',
            'name_en'                => 'nullable|string|max:255',
            'email'                  => 'required|email|unique:users,email,' . $student->user_id . '|unique:students,email,' . $student->id,
            'pledge'                 => 'required|accepted',
            'student_number'         => 'required|string|unique:students,student_number,' . $student->id,
            'phone'                  => 'required|string|max:20',
            'nationality'            => 'required|string|max:100',
            'date_of_birth'          => 'required|date',
            'place_of_birth'         => 'nullable|string|max:100',
            'id_card_number'         => 'nullable|string|max:50',
            'id_card_source'         => 'nullable|string|max:100',
            'id_card_date'           => 'nullable|date',
            'city'                   => 'nullable|string|max:100',
            'marital_status'         => 'nullable|string|max:50',
            'dependents_count'       => 'nullable|integer|min:0|max:30',
            'health_status'          => 'nullable|in:good,average,weak',
            // Address
            'governorate'            => 'nullable|string|max:100',
            'district'               => 'nullable|string|max:100',
            'village'                => 'nullable|string|max:100',
            'home_phone'             => 'nullable|string|max:20',
            'permanent_address'      => 'nullable|string|max:255',
            // Education - Last certificate
            'last_certificate'       => 'nullable|string|max:150',
            'last_cert_major'        => 'nullable|string|max:100',
            'last_cert_grade'        => 'nullable|string|max:50',
            'graduation_year'        => 'nullable|digits:4|integer|min:1990|max:2100',
            'graduated_school'       => 'nullable|string|max:150',
            // University
            'university'             => 'required|string|max:150',
            'college'                => 'required|string|max:150',
            'major'                  => 'required|string|max:150',
            'academic_level'         => 'required|string|max:20',
            'enrollment_date'        => 'nullable|date',
            'study_duration'         => 'nullable|string|max:50',
            'remaining_period'       => 'nullable|string|max:50',
            'expected_graduation'    => 'nullable|date',
            'current_academic_year'  => 'nullable|string|max:20',
            'skills'                 => 'nullable|string|max:500',
            // Guardian / Emergency
            'guardian_name'          => 'required|string|max:100',
            'guardian_relation'      => 'required|string|max:50',
            'guardian_education'     => 'nullable|string|max:100',
            'guardian_phone'         => 'required|string|max:20',
            'guardian_job'           => 'nullable|string|max:100',
            'emergency_name'         => 'nullable|string|max:100',
            'emergency_relation'     => 'nullable|string|max:50',
            'emergency_phone'        => 'nullable|string|max:20',
            // Family
            'family_males'           => 'nullable|integer|min:0|max:30',
            'family_females'         => 'nullable|integer|min:0|max:30',
            'family_avg_income'      => 'nullable|numeric|min:0',
            // Family workers - arrays
            'workers'                => 'nullable|array|max:10',
            'workers.*.name'         => 'nullable|string|max:100',
            'workers.*.job'          => 'nullable|string|max:100',
            'workers.*.organization' => 'nullable|string|max:100',
            'workers.*.phone'        => 'nullable|string|max:20',
            // Documents
            'photo'                  => ($student->photo ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'id_card_file'           => ($student->id_card_file ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'certificate_file'       => ($student->certificate_file ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'university_card_file'   => ($student->university_card_file ? 'nullable' : 'required') . '|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'required' => 'هذا الحقل مطلوب.',
            'email' => 'يرجى إدخال بريد إلكتروني صحيح.',
            'unique' => 'هذه القيمة مسجلة مسبقاً، يرجى التأكد منها.',
            'mimes' => 'نوع الملف غير مدعوم، يرجى رفع (JPG, PNG, PDF).',
            'max' => 'حجم الملف كبير جداً، الحد الأقصى 2 ميجابايت.',
            'date' => 'يرجى إدخال تاريخ صحيح.',
            'integer' => 'يجب إدخال رقم صحيح.',
            'numeric' => 'يجب إدخال قيمة عددية.',
            'pledge.required' => 'يجب الموافقة على التعهد والإقرار للمتابعة.',
        ]);

        // Handle File Uploads
        $fileData = [];
        $docs = ['photo', 'id_card_file', 'certificate_file', 'university_card_file'];
        foreach ($docs as $doc) {
            if ($request->hasFile($doc)) {
                // Delete old file if exists
                if ($student->$doc) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($student->$doc);
                }
                $fileData[$doc] = $request->file($doc)->store('students/documents', 'public');
            }
        }

        // Build family_workers JSON
        $familyWorkers = [];
        if ($request->has('workers')) {
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
        }

        $student->update(array_merge($validated, $fileData, [
            'family_workers'      => $familyWorkers ?: null,
            'is_profile_approved' => false,
            'can_edit_profile'    => false,
        ]));

        $user->update([
            'profile_completed' => true,
            'email' => $validated['email'],
            'name' => $validated['name_ar'],
        ]);

        return redirect()->route('dashboard')->with('success', 'تم حفظ بياناتك بنجاح، بانتظار موافقة الإدارة.');
    }

    public function autoSaveProfile(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student record not found.'], 404);
        }

        // We only validate the fields that are sent.
        $data = $request->except(['_token', '_method', 'photo', 'id_card_file', 'certificate_file', 'university_card_file', 'workers']);

        // Handle family workers if sent
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
            $data['family_workers'] = $familyWorkers ?: null;
        }

        // Handle step progress
        if ($request->has('profile_step')) {
            $data['profile_step'] = $request->input('profile_step');
        }
        if ($request->has('profile_completion')) {
            $data['profile_completion'] = $request->input('profile_completion');
        }

        $student->update($data);

        return response()->json(['success' => true, 'message' => 'تم الحفظ بنجاح']);
    }
}
