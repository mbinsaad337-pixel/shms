@extends(auth()->user()->hasRole('nutrition-manager') ? 'layouts.nutrition' : 'layouts.app')
@section('title', 'تفاصيل السند ' . $voucher->voucher_number)

@section('content')
    <div class="p-6 max-w-2xl mx-auto">
        @include('partials.print_header', ['title' => $voucher->getTypeLabel() . ' - ' . $voucher->voucher_number, 'number' => $voucher->voucher_number])

        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('nutrition.vouchers.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-cairo">{{ $voucher->getTypeLabel() }}</h2>
                    <p class="text-gray-400   text-sm">{{ $voucher->voucher_number }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('nutrition.vouchers.export-pdf', $voucher) }}"
                    class="w-9 h-9 bg-gray-800 text-white rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-pdf text-sm"></i>
                </a>
                @if($voucher->status === 'active')
                    <form action="{{ route('nutrition.vouchers.cancel', $voucher) }}" method="POST"
                        onsubmit="return confirm('هل تريد إلغاء هذا السند؟')">
                        @csrf
                        <button type="submit"
                            class="w-9 h-9 bg-red-100 text-red-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ban text-sm"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Voucher Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Banner -->
            <div
                class="p-6 {{ $voucher->type === 'payment' ? 'bg-gradient-to-l from-red-500 to-rose-600' : 'bg-gradient-to-l from-green-500 to-emerald-600' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white/70 text-sm font-cairo">
                            {{ $voucher->type === 'payment' ? 'سند صرف' : 'سند قبض' }}
                        </p>
                        <p class="text-white text-4xl font-black   mt-1">{{ number_format($voucher->amount, 2) }}
                        </p>
                        <p class="text-white/80 font-cairo mt-0.5">ريال سعودي</p>
                    </div>
                    <div class="text-right">
                        <p class="text-white/70 text-xs font-cairo">الحالة</p>
                        <span
                            class="px-3 py-1 rounded-lg text-sm font-bold mt-1 inline-block
                                {{ $voucher->status === 'active' ? 'bg-white/20 text-white' : 'bg-white/20 text-white/60 line-through' }}">
                            {{ $voucher->status === 'active' ? 'فعال' : 'ملغي' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs font-bold text-gray-400 font-cairo uppercase mb-1">التاريخ</p>
                        <p class="font-bold text-gray-800  ">{{ $voucher->voucher_date->format('Y-m-d') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs font-bold text-gray-400 font-cairo uppercase mb-1">أُنشئ بواسطة</p>
                        <p class="font-bold text-gray-800 font-cairo">{{ $voucher->creator?->name }}</p>
                    </div>
                </div>

                @if($voucher->supplier)
                    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4">
                        <p class="text-xs font-bold text-orange-400 font-cairo mb-1">المورد</p>
                        <p class="font-bold text-orange-800 font-cairo text-lg">{{ $voucher->supplier->name }}</p>
                    </div>
                @endif
                @if($voucher->student)
                    <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4">
                        <p class="text-xs font-bold text-purple-400 font-cairo mb-1">الطالب</p>
                        <p class="font-bold text-purple-800 font-cairo text-lg">{{ $voucher->student?->name_ar ?? 'طالب غير موجود' }}</p>
                        <p class="text-purple-400   text-xs">{{ $voucher->student->university_id }}</p>
                    </div>
                @endif

                <div class="bg-gray-50 rounded-2xl p-4">
                    <p class="text-xs font-bold text-gray-400 font-cairo mb-1">البيان</p>
                    <p class="text-gray-700 font-almarai">{{ $voucher->description }}</p>
                </div>

                @if($voucher->attachment)
                    <div>
                        <p class="text-xs font-bold text-gray-400 font-cairo mb-2">المرفق</p>
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($voucher->attachment) }}" target="_blank"
                            class="inline-flex items-center gap-2 text-blue-600 hover:underline font-cairo text-sm">
                            <i class="fas fa-paperclip"></i> عرض المرفق
                        </a>
                    </div>
                @endif
            </div>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
