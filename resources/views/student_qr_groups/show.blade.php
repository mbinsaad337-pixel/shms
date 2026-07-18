@extends('layouts.app')

@section('title', 'عرض الرمز المجمع')

@section('content')
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <a href="{{ route('student-qr-groups.index') }}"
                    class="text-navy hover:text-blue-700 font-bold mb-4 inline-flex items-center font-cairo">
                    <i class="fas fa-arrow-right ml-2"></i> العودة للقائمة
                </a>
                <h1 class="text-3xl font-extrabold text-navy font-cairo">تفاصيل الرمز المجمع</h1>
            </div>
            <button onclick="window.print()"
                class="bg-white border-2 border-navy text-navy px-6 py-2 rounded-xl font-bold hover:bg-navy hover:text-white transition-all font-cairo no-print">
                <i class="fas fa-print ml-2"></i> طباعة الرمز
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- QR Display -->
            <div class="card-premium p-8 flex flex-col items-center justify-center bg-gradient-to-br from-white to-gray-50">
                <div class="mb-6 p-4 bg-white rounded-3xl shadow-xl border border-gray-100 flex justify-center">
                    {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->color(0, 66, 116)->generate($qrData) !!}
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 font-almarai mb-1">الرمز التعريفي</p>
                    <div class="flex items-center justify-center gap-2 flex-wrap">
                        <code id="tokenCode"
                            class="text-navy font-bold text-sm bg-blue-50 px-3 py-1 rounded-md">{{ $studentQrGroup->group_token }}</code>
                        <button onclick="copyToken()"
                            class="bg-navy text-white px-3 py-1 rounded-lg text-xs font-cairo hover:bg-sky-900 transition-all"
                            title="نسخ التوكن للإدخال اليدوي في نظام التغذية">
                            <i class="fas fa-copy ml-1"></i> نسخ
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 font-almarai mt-1">يمكن لصقه يدوياً في صفحة توزيع الوجبات</p>
                </div>

                <div class="mt-6 w-full pt-6 border-t border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-500 font-almarai">الحالة:</span>
                        <span
                            class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold font-cairo">نشط</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500 font-almarai">ينتهي في:</span>
                        <span
                            class="text-sm font-bold text-red-600 font-almarai">{{ $studentQrGroup->expires_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Group Info -->
            <div class="space-y-6">
                <div class="card-premium p-6">
                    <h3 class="text-lg font-bold text-navy mb-4 font-cairo border-b pb-2">الطالب الرئيسي (المسؤول)</h3>
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-12 h-12 bg-gold/10 text-gold rounded-2xl flex items-center justify-center text-xl">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div>
                            <p class="font-bold text-navy font-cairo">{{ $studentQrGroup->primaryStudent->name_ar }}</p>
                            <p class="text-xs text-gray-500 font-almarai">
                                {{ $studentQrGroup->primaryStudent->university_id }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-premium p-6">
                    <h3 class="text-lg font-bold text-navy mb-4 font-cairo border-b pb-2">الطلاب المشمولون
                        ({{ $studentQrGroup->students->count() + 1 }})</h3>
                    <div class="space-y-4 max-h-80 overflow-y-auto pr-2">
                        <!-- Primary again in the list for clarity -->
                        <div class="flex items-center justify-between p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <span
                                    class="w-8 h-8 bg-blue-100 text-navy rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                                <div>
                                    <p class="text-sm font-bold text-navy font-cairo">
                                        {{ $studentQrGroup->primaryStudent->name_ar }} (أنت)
                                    </p>
                                    <p class="text-[10px] text-gray-500 font-almarai">
                                        {{ $studentQrGroup->primaryStudent->university_id }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @foreach($studentQrGroup->students as $index => $member)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:bg-white hover:shadow-sm transition-all">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <span
                                        class="w-8 h-8 bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center text-xs font-bold">{{ $index + 2 }}</span>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 font-cairo">{{ $member->name_ar }}</p>
                                        <p class="text-[10px] text-gray-500 font-almarai">{{ $member->university_id }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card-premium {
                box-shadow: none !important;
                border: 1px solid #eee !important;
            }

            body {
                background: white !important;
            }

            main {
                padding: 0 !important;
            }
        }
    </style>
@endsection
@push('scripts')
<script>
    function copyToken() {
        const token = document.getElementById('tokenCode').textContent.trim();
        navigator.clipboard.writeText(token).then(() => {
            Swal.fire({ icon: 'success', title: '�� �����!', text: '���� ������ �� ���� ����� ������� � ����� ����', toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
        }).catch(() => { prompt('���� ��� ������:', token); });
    }
</script>
@endpush
