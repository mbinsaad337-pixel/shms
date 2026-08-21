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
        return view('quran-circles.profile.edit', compact('user'));
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
        return view('quran-circles.profile.change-password');
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
            'student_number'         => 'nullable|string|unique:students,student_number,' . $student->id,
            'phone'                  => 'required|string|max:20',
            'nationality'            => 'nullable|string|max:100',
            'date_of_birth'          => 'nullable|date',
            'place_of_birth'         => 'nullable|string|max:100',
            'id_card_number'         => 'nullable|string|max:50',
            'marital_status'         => 'nullable|string|max:50',
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
            'expected_graduation'    => 'nullable|string|max:50',
            'skills'                 => 'nullable|string|max:500',
            // Emergency
            'emergency_name'         => 'nullable|string|max:100',
            'emergency_relation'     => 'nullable|string|max:50',
            'emergency_phone'        => 'nullable|string|max:20',
        ], [
            'required' => 'هذا الحقل مطلوب.',
            'email' => 'يرجى إدخال بريد إلكتروني صحيح.',
            'unique' => 'هذه القيمة مسجلة مسبقاً، يرجى التأكد منها.',
            'date' => 'يرجى إدخال تاريخ صحيح.',
            'integer' => 'يجب إدخال رقم صحيح.',
        ]);

        $student->update($validated);

        $user->update([
            'email' => $validated['email'],
            'name' => $validated['name_ar'],
        ]);

        return redirect()->route('dashboard')->with('success', 'تم تحديث بياناتك بنجاح.');
    }

    public function autoSaveProfile(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student record not found.'], 404);
        }

        $allowedFields = [
            'name_ar', 'name_en', 'phone', 'nationality', 'date_of_birth',
            'place_of_birth', 'id_card_number', 'marital_status', 'health_status',
            'governorate', 'district', 'village', 'home_phone', 'permanent_address',
            'last_certificate', 'last_cert_major', 'last_cert_grade', 'graduation_year',
            'graduated_school', 'university', 'college', 'major', 'academic_level',
            'enrollment_date', 'expected_graduation', 'skills', 'student_number',
            'emergency_name', 'emergency_relation', 'emergency_phone',
            'profile_step', 'profile_completion',
        ];

        $data = $request->only($allowedFields);

        if (empty($data)) {
            return response()->json(['success' => true, 'message' => 'لا توجد تغييرات']);
        }

        $validated = \Illuminate\Support\Facades\Validator::make($data, [
            'name_ar'            => 'sometimes|string|max:255',
            'phone'              => 'sometimes|string|max:20',
            'university'         => 'sometimes|string|max:150',
            'college'            => 'sometimes|string|max:150',
            'major'              => 'sometimes|string|max:150',
            'academic_level'     => 'sometimes|string|max:20',
            'profile_step'       => 'sometimes|integer|min:1|max:5',
            'profile_completion' => 'sometimes|integer|min:0|max:100',
            'date_of_birth'      => 'sometimes|nullable|date',
            'graduation_year'    => 'sometimes|nullable|digits:4|integer|min:1990|max:2100',
        ])->validate();

        $student->update($validated);

        return response()->json(['success' => true, 'message' => 'تم الحفظ بنجاح']);
    }
}
