<?php

namespace App\Services;

/**
 * خدمة إرسال رسائل واتساب عبر WhatsApp Web (wa.me)
 *
 * تعمل بنفس أسلوب إرسال بيانات الطالب الجديد:
 * تُنشئ رابط wa.me ويُحفظ في الـ session لتظهر نافذة SweetAlert
 * تمنح المستخدم خيار فتح واتساب وإرسال الرسالة يدوياً.
 */
class WhatsAppService
{
    /**
     * إنشاء رابط wa.me وحفظه في session لإظهار نافذة الإرسال
     *
     * @param  string|null  $phone   رقم هاتف الطالب
     * @param  string       $message نص الرسالة
     * @param  string       $studentName اسم الطالب (يفيد في الإرسال الجماعي)
     */
    public function flash(?string $phone, string $message, string $studentName = 'الطالب'): void
    {
        if (empty($phone)) {
            return;
        }

        $phone = $this->normalizePhone($phone);
        $url   = 'https://wa.me/' . $phone . '?text=' . urlencode($message);

        // إضافة الرابط إلى المصفوفة لدعم الإرسال الجماعي
        $links = session()->get('whatsapp_links', []);
        $links[] = [
            'name' => $studentName,
            'url'  => $url
        ];
        
        session()->flash('whatsapp_links', $links);
    }

    // ──────────────────────────────────────────────────────────────
    // رسائل جاهزة حسب نوع الحدث
    // ──────────────────────────────────────────────────────────────

    /** رسالة تسجيل مخالفة */
    public function violationMessage(string $studentName, string $type, string $severity, string $date): string
    {
        $severityLabel = match ($severity) {
            'minor'    => 'بسيطة',
            'moderate' => 'متوسطة',
            'severe'   => 'جسيمة',
            default    => $severity,
        };

        return "السلام عليكم ورحمة الله وبركاته\n\n"
            . "📋 *إشعار مخالفة*\n"
            . "الطالب: *{$studentName}*\n\n"
            . "نُحيطكم علماً بأنه تم تسجيل مخالفة بحقكم:\n"
            . "• النوع: {$type}\n"
            . "• الدرجة: {$severityLabel}\n"
            . "• التاريخ: {$date}\n\n"
            . "يُرجى مراجعة الإدارة للاطلاع على التفاصيل.";
    }

    /** رسالة تطبيق عقوبة */
    public function penaltyMessage(string $studentName, string $type, ?string $startDate, ?string $endDate): string
    {
        $typeLabel = match ($type) {
            'verbal_warning'       => 'إنذار شفهي',
            'written_warning'      => 'إنذار كتابي',
            'service_suspension'   => 'إيقاف الخدمات',
            'temporary_suspension' => 'إيقاف مؤقت',
            'expulsion'            => 'فصل',
            default                => $type,
        };

        $dates = '';
        if ($startDate) {
            $dates .= "\n• من: {$startDate}";
            if ($endDate) $dates .= "\n• إلى: {$endDate}";
        }

        return "السلام عليكم ورحمة الله وبركاته\n\n"
            . "⚠️ *إشعار عقوبة*\n"
            . "الطالب: *{$studentName}*\n\n"
            . "نُحيطكم علماً بأنه تم تطبيق عقوبة بحقكم:\n"
            . "• النوع: {$typeLabel}"
            . $dates . "\n\n"
            . "للاستفسار يُرجى مراجعة الإدارة.";
    }

    /** رسالة تسجيل تعهد */
    public function commitmentMessage(string $studentName, string $title, string $date): string
    {
        return "السلام عليكم ورحمة الله وبركاته\n\n"
            . "📝 *إشعار تعهد*\n"
            . "الطالب: *{$studentName}*\n\n"
            . "نُحيطكم علماً بأنه تم تسجيل تعهد باسمكم:\n"
            . "• العنوان: {$title}\n"
            . "• التاريخ: {$date}\n\n"
            . "يُرجى الالتزام بما جاء في التعهد.";
    }

    /** رسالة الموافقة على استئذان */
    public function leaveApprovalMessage(string $studentName, string $type, string $departureDate, ?string $returnDate): string
    {
        $typeLabel = match ($type) {
            'temporary' => 'استئذان مؤقت',
            'vacation'  => 'إجازة',
            'medical'   => 'استئذان طبي',
            'lateness'  => 'تأخر',
            default     => $type,
        };

        $returnInfo = $returnDate ? "\n• تاريخ العودة المتوقع: {$returnDate}" : '';

        return "السلام عليكم ورحمة الله وبركاته\n\n"
            . "✅ *إشعار موافقة على استئذان*\n"
            . "الطالب: *{$studentName}*\n\n"
            . "يسعدنا إبلاغكم بأنه تمت الموافقة على طلب استئذانكم:\n"
            . "• النوع: {$typeLabel}\n"
            . "• تاريخ المغادرة: {$departureDate}"
            . $returnInfo . "\n\n"
            . "نتمنى لكم رحلة طيبة 🏠";
    }

    /** رسالة تسجيل غياب */
    public function absenceMessage(string $studentName, string $date, bool $hasExcuse): string
    {
        $status = $hasExcuse ? 'غياب بعذر ✅' : 'غياب بدون عذر ❌';
        $note   = $hasExcuse ? '' : "\nيُرجى تقديم عذر مقبول لإدارة المركز.";

        return "السلام عليكم ورحمة الله وبركاته\n\n"
            . "🔴 *إشعار غياب*\n"
            . "الطالب: *{$studentName}*\n\n"
            . "نُحيطكم علماً بأنه تم تسجيل غياب:\n"
            . "• التاريخ: {$date}\n"
            . "• الحالة: {$status}"
            . $note;
    }

    // ──────────────────────────────────────────────────────────────
    // مساعدات
    // ──────────────────────────────────────────────────────────────

    /**
     * تنظيف رقم الهاتف واكتشاف مفتاح الدولة تلقائياً
     *
     * السعودية : يبدأ بـ 05 أو 5  → مفتاح 966
     * اليمن    : يبدأ بـ 07 أو 7  → مفتاح 967
     * إذا كان المفتاح موجوداً مسبقاً (966 أو 967) → يُبقى كما هو
     */
    public function normalizePhone(string $phone): string
    {
        // إزالة كل ما ليس رقماً
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // إذا كان المفتاح موجوداً مسبقاً → لا تغيير
        if (str_starts_with($phone, '966') || str_starts_with($phone, '967')) {
            return $phone;
        }

        // إذا بدأ بـ 0 → إزالة الصفر ثم اكتشاف المفتاح
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        // اكتشاف المفتاح بناءً على أول رقم
        // السعودية: 5xxxxxxxx (9 أرقام تبدأ بـ 5)
        // اليمن:    7xxxxxxxx (9 أرقام تبدأ بـ 7)
        if (str_starts_with($phone, '5')) {
            return '966' . $phone; // سعودي
        }

        if (str_starts_with($phone, '7')) {
            return '967' . $phone; // يمني
        }

        // افتراضي: أضف مفتاح اليمن
        return '967' . $phone;
    }
}
