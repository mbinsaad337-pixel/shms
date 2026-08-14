<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\GraduationAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GraduationController extends Controller
{
    /**
     * عرض نموذج استكمال بيانات التخرج (للطالب)
     */
    public function showForm(Student $student)
    {
        $user = auth()->user();

        // يجب أن يكون الطالب نفسه
        if (!$user->hasRole('student') || $student->user_id !== $user->id) {
            abort(403);
        }

        // لا يمكن التقديم مرة أخرى إذا كان الطلب منتظراً أو موافقاً
        if (in_array($student->graduation_request_status, ['pending', 'approved'])) {
            return back()->with('info', 'تم تقديم طلب التخرج مسبقاً وهو قيد المراجعة.');
        }

        return view('students.graduation-form', compact('student'));
    }

    /**
     * حفظ طلب التخرج (مرفقات + مسمى وظيفي)
     */
    public function submitRequest(Request $request, Student $student)
    {
        $user = auth()->user();

        if (!$user->hasRole('student') || $student->user_id !== $user->id) {
            abort(403);
        }

        // لا يمكن التقديم مرة أخرى
        if (in_array($student->graduation_request_status, ['pending', 'approved'])) {
            return back()->with('error', 'تم تقديم طلب التخرج مسبقاً.');
        }

        $request->validate([
            'job_title'                => 'required|string|max:255',
            'attachments'              => 'required|array|min:1',
            'attachments.*.name'       => 'required|string|max:255',
            'attachments.*.file'       => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ], [
            'job_title.required'             => 'يجب اختيار المسمى الوظيفي أو إدخاله.',
            'attachments.required'           => 'يجب إرفاق مرفق واحد على الأقل.',
            'attachments.min'                => 'يجب إرفاق مرفق واحد على الأقل.',
            'attachments.*.name.required'    => 'يجب تسمية كل مرفق.',
            'attachments.*.file.required'    => 'يجب رفع ملف لكل مرفق.',
            'attachments.*.file.max'         => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
            'attachments.*.file.mimes'       => 'أنواع الملفات المقبولة: PDF, JPG, PNG, DOC, DOCX.',
        ]);

        DB::transaction(function () use ($request, $student) {
            // حذف المرفقات القديمة في حالة إعادة التقديم بعد رفض
            foreach ($student->graduationAttachments as $old) {
                Storage::disk('public')->delete($old->file_path);
                $old->delete();
            }

            // رفع المرفقات الجديدة
            foreach ($request->attachments as $item) {
                $path = $item['file']->store('graduation_attachments/' . $student->id, 'public');
                GraduationAttachment::create([
                    'student_id' => $student->id,
                    'name'       => $item['name'],
                    'file_path'  => $path,
                    'file_type'  => $item['file']->getClientMimeType(),
                ]);
            }

            // تحديث حالة الطالب
            $student->update([
                'job_title'                  => $request->job_title,
                'graduation_request_status'  => 'pending',
                'graduation_requested_at'    => now(),
                'graduation_rejection_reason'=> null,
            ]);
        });

        return redirect()->route('students.show', $student)
            ->with('success', 'تم تقديم طلب استكمال بيانات التخرج بنجاح. سيتم مراجعته من قِبل مدير السكن.');
    }

    /**
     * قائمة الخريجين في الانتظار (للمدير)
     */
    public function pendingList(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        $query = Student::query()
            ->where('graduation_request_status', 'pending')
            ->where('is_graduate', false);

        if ($user->center_id) {
            $query->where('center_id', $user->center_id);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', '%' . $request->search . '%')
                    ->orWhere('student_number', 'like', '%' . $request->search . '%')
                    ->orWhere('national_id', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->with(['center', 'user', 'program', 'graduationAttachments'])
            ->latest('graduation_requested_at')
            ->paginate(20)
            ->withQueryString();

        return view('students.graduation-pending', compact('students'));
    }

    /**
     * الموافقة على طلب التخرج
     */
    public function approve(Student $student)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        if ($student->graduation_request_status !== 'pending') {
            return back()->with('error', 'هذا الطلب ليس في انتظار المراجعة.');
        }

        DB::transaction(function () use ($student) {
            // نقل الطالب للخريجين
            $student->update([
                'is_graduate'               => true,
                'status'                    => 'graduated',
                'graduation_year'           => now()->year,
                'graduation_request_status' => 'approved',
            ]);

            // إخلاء الغرفة إن وجدت
            $activeAssignment = $student->activeRoomAssignment;
            if ($activeAssignment) {
                $activeAssignment->update([
                    'released_at'    => now(),
                    'release_reason' => 'تخرج',
                ]);
            }

            // إغلاق اشتراك التغذية
            $activeFoodSub = $student->activeFoodSubscription;
            if ($activeFoodSub) {
                $activeFoodSub->update(['status' => 'expired']);
            }
        });

        return redirect()->route('graduation.pending')
            ->with('success', 'تمت الموافقة على طلب التخرج ونُقل الطالب إلى قائمة الخريجين.');
    }

    /**
     * رفض طلب التخرج
     */
    public function reject(Request $request, Student $student)
    {
        $user = auth()->user();
        if (!$user->hasRole('center-manager') && !$user->hasRole('super-admin')) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ], [
            'rejection_reason.required' => 'يجب ذكر سبب الرفض.',
        ]);

        $student->update([
            'graduation_request_status'    => 'rejected',
            'graduation_rejection_reason'  => $request->rejection_reason,
        ]);

        return redirect()->route('graduation.pending')
            ->with('success', 'تم رفض طلب التخرج وإعادة الطالب إلى قائمة الطلاب.');
    }
}
