@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'QR المجمع')

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-6 no-print">
            <a href="{{ route('nutrition.qr-groups.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 font-cairo">QR المجمع</h2>
        </div>

        @if(!$qrGroup->isValid())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-5 text-center no-print">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mb-2 block"></i>
                <p class="font-bold text-red-700 font-cairo">
                    {{ $qrGroup->is_used ? 'هذا QR استُخدم مسبقاً' : 'انتهت صلاحية هذا QR' }}
                </p>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-5 text-center no-print">
                <p class="font-bold text-green-700 font-cairo"><i class="fas fa-check-circle ml-1"></i>QR فعال — صالح حتى نهاية
                    اليوم</p>
            </div>
        @endif

        <!-- QR Card - printable -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 text-center" id="qrCard">
            <div class="mb-4">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <i class="fas fa-layer-group text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-black text-gray-800 font-cairo">QR توزيع مجمع</h3>
                <p class="text-gray-400 font-cairo text-sm mt-1">{{ $qrGroup->members_count }} أعضاء</p>
            </div>

            <!-- QR Code Display -->
            <div class="flex justify-center my-6">
                <div id="qrContainer" class="p-4 bg-white border-4 border-teal-400 rounded-2xl shadow-inner inline-block">
                </div>
            </div>

            <p class="font-mono text-xs text-gray-300 mt-2 no-print">{{ $qrGroup->qr_code }}</p>
            <p class="font-cairo text-sm text-gray-500 mt-3">
                <i class="fas fa-calendar-day ml-1"></i>صالح في: {{ $qrGroup->valid_date->format('Y-m-d') }}
            </p>

            <!-- Members List -->
            <div class="mt-6 text-right">
                <h4 class="font-bold text-gray-700 font-cairo mb-3 text-sm">أعضاء المجموعة:</h4>
                <div class="space-y-2">
                    @foreach($qrGroup->members as $member)
                        <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-2.5">
                            <div class="w-7 h-7 bg-teal-100 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-user text-teal-600 text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800 font-cairo text-sm">{{ $member->student?->name_ar ?? 'طالب غير موجود' }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $member->student?->university_id }}</p>
                            </div>
                            @if($member->subscription)
                                <span
                                    class="text-[9px] bg-green-100 text-green-700 px-2 py-0.5 rounded font-cairo font-bold">مشترك</span>
                            @else
                                <span class="text-[9px] bg-red-100 text-red-500 px-2 py-0.5 rounded font-cairo font-bold">غير
                                    مشترك</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 flex gap-3 no-print">
                <button onclick="window.print()"
                    class="flex-1 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-2xl font-bold font-cairo transition-all">
                    <i class="fas fa-print ml-2"></i> طباعة QR
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById("qrContainer"), {
            text: "{{ $qrGroup->qr_code }}",
            width: 220,
            height: 220,
            colorDark: "#0d9488",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>
@endpush
