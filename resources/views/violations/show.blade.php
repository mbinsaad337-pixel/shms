@extends('layouts.app')

@section('title', 'تفاصيل المخالفة')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-navy shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo text-right">تفاصيل المخالفة</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2 text-right">متابعة حالة المخالفة المسجلة للطالب: {{ $violation->student->name_ar }}</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('violations.export', $violation->id) }}" target="_blank"
                    class="px-6 py-3 bg-red-50 text-red-600 rounded-2xl hover:bg-red-500 hover:text-white font-cairo font-bold transition-all flex items-center gap-2 border border-red-100 shadow-sm">
                    <i class="fas fa-file-pdf"></i>
                    <span>تصدير تفاصيل المخالفة PDF</span>
                </a>
                <a href="{{ route('violations.index') }}"
                    class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Violation Details Column -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 relative overflow-hidden">
                    <!-- Severity Watermark -->
                    <div class="absolute -left-10 -top-10 opacity-[0.03] select-none pointer-events-none">
                        <i class="fas fa-gavel text-[15rem] rotate-12"></i>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-50">
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase tracking-widest font-cairo mb-2">نوع المخالفة</span>
                                <h2 class="text-2xl font-black text-navy font-cairo">{{ $violation->type }}</h2>
                            </div>
                            <div class="text-left">
                                @php
                                    $severityClasses = [
                                        'minor' => 'bg-blue-50 text-blue-600 border-blue-100',
                                        'moderate' => 'bg-orange-50 text-orange-600 border-orange-100',
                                        'severe' => 'bg-red-50 text-red-600 border-red-100'
                                    ];
                                    $severityLabels = [
                                        'minor' => 'مخالفة بسيطة',
                                        'moderate' => 'مخالفة متوسطة',
                                        'severe' => 'مخالفة جسيمة'
                                    ];
                                @endphp
                                <span class="px-6 py-3 rounded-2xl text-sm font-black border {{ $severityClasses[$violation->severity] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                    {{ $severityLabels[$violation->severity] ?? $violation->severity }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-8">
                            <div>
                                <span class="block text-xs text-gray-400 font-bold uppercase tracking-widest font-cairo mb-4">وصف وتفاصيل المخالفة</span>
                                <div class="bg-gray-50 p-8 rounded-3xl border border-gray-100 text-navy font-almarai leading-relaxed text-lg shadow-inner">
                                    {{ $violation->description }}
                                </div>
                            </div>

                            @if($violation->penalty)
                            <div class="mt-12 pt-10 border-t-2 border-dashed border-gray-100">
                                <h3 class="text-xl font-black text-navy font-cairo mb-6 flex items-center gap-3">
                                    <i class="fas fa-gavel text-red-500"></i>
                                    <span>العقوبة المسندة</span>
                                </h3>
                                <div class="bg-red-50/50 rounded-[2rem] p-8 border border-red-100 flex flex-col md:flex-row gap-8 items-start">
                                    <div class="flex-1 space-y-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $penaltyTypes = [
                                                    'verbal_warning' => 'تنبيه شفوي',
                                                    'written_warning' => 'إنذار كتابي',
                                                    'service_suspension' => ' عقوبة خدامات (تحميل/صيانة)  ',
                                                    'temporary_suspension' => 'فصل مؤقت من السكن',
                                                    'expulsion' => 'فصل نهائي'
                                                ];
                                            @endphp
                                            <span class="text-xs font-bold text-gray-400 font-cairo">نوع العقوبة:</span>
                                            <span class="px-4 py-1.5 bg-red-600 text-white rounded-xl text-xs font-black">{{ $penaltyTypes[$violation->penalty->type] ?? $violation->penalty->type }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-gray-400 font-cairo block mb-2">وصف العقوبة:</span>
                                            <p class="text-navy font-bold font-almarai text-sm leading-relaxed">{{ $violation->penalty->description }}</p>
                                        </div>
                                    </div>
                                    <div class="w-full md:w-64 bg-white p-6 rounded-2xl border border-red-100 shadow-sm space-y-4">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 font-bold font-cairo uppercase">تاريخ البدء</span>
                                            <span class="text-navy font-black text-xs font-mono">{{ $violation->penalty->start_date?->format('Y-m-d') ?? '---' }}</span>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] text-gray-400 font-bold font-cairo uppercase">تاريخ الانتهاء</span>
                                            <span class="text-red-600 font-black text-xs font-mono">{{ $violation->penalty->end_date?->format('Y-m-d') ?? 'مفتوح' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="mt-12 pt-10 border-t-2 border-dashed border-gray-100">
                                <h3 class="text-xl font-black text-navy font-cairo mb-6 flex items-center gap-3">
                                    <i class="fas fa-balance-scale text-orange-500"></i>
                                    <span>إسناد عقوبة انضباطية</span>
                                </h3>
                                
                                <form action="{{ route('penalties.store') }}" method="POST" class="bg-gray-50/50 rounded-[2rem] p-10 border border-gray-100">
                                    @csrf
                                    <input type="hidden" name="violation_id" value="{{ $violation->id }}">
                                    <input type="hidden" name="student_id" value="{{ $violation->student_id }}">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">نوع العقوبة</label>
                                                <div class="relative">
                                                    <select name="type" required
                                                        class="w-full pl-5 pr-12 py-4 rounded-2xl border-2 border-gray-100 bg-white focus:bg-white focus:border-red-300 outline-none text-right font-almarai appearance-none transition-all shadow-sm">
                                                        <option value="">-- اختر نوع العقوبة --</option>
                                                        <option value="verbal_warning">تنبيه شفوي</option>
                                                        <option value="written_warning">إنذار كتابي</option>
                                                        <option value="service_suspension">حرمان من الخدمات المؤقت</option>
                                                        <option value="temporary_suspension">فصل مؤقت من السكن</option>
                                                        <option value="expulsion">فصل نهائي</option>
                                                    </select>
                                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                                        <i class="fas fa-hand-holding-hand text-gray-400"></i>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">فترة سريان العقوبة (اختياري)</label>
                                                <div class="flex gap-4">
                                                    <div class="flex-1">
                                                        <span class="block text-[10px] text-gray-400 font-bold mb-1 text-right">من</span>
                                                        <input type="date" name="start_date" 
                                                            class="w-full px-5 py-3 rounded-xl border-2 border-gray-100 bg-white focus:border-red-300 outline-none font-mono text-xs">
                                                    </div>
                                                    <div class="flex-1">
                                                        <span class="block text-[10px] text-gray-400 font-bold mb-1 text-right">إلى</span>
                                                        <input type="date" name="end_date" 
                                                            class="w-full px-5 py-3 rounded-xl border-2 border-gray-100 bg-white focus:border-red-300 outline-none font-mono text-xs">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">وصف إسناد العقوبة</label>
                                                <textarea name="description" rows="5" required placeholder="اشرح مبررات العقوبة وتفاصيلها..."
                                                    class="w-full px-6 py-4 rounded-3xl border-2 border-gray-100 bg-white focus:bg-white focus:border-red-300 outline-none text-right font-almarai shadow-sm"></textarea>
                                            </div>
                                            
                                            <button type="submit" class="w-full py-4 bg-orange-500 text-white rounded-2xl font-black font-cairo hover:bg-orange-600 shadow-lg shadow-orange-500/30 transition-all flex items-center justify-center gap-3">
                                                <i class="fas fa-check-double"></i>
                                                <span>اعتماد وإسناد العقوبة</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @endif

                            @if($violation->attachments)
                            <div class="mt-12 pt-10 border-t-2 border-dashed border-gray-100">
                                <span class="block text-xs text-gray-400 font-bold uppercase tracking-widest font-cairo mb-6 flex items-center gap-2">
                                    <i class="fas fa-paperclip text-gold"></i> المرفقات والوثائق المؤرشفة
                                </span>
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                    @foreach($violation->attachments as $attachment)
                                        <div class="group relative bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 hover:border-navy transition-all shadow-sm">
                                            @php
                                                $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                            @endphp

                                            @if($isImage)
                                                <img src="{{ asset('storage/' . $attachment) }}" 
                                                     class="w-full h-32 object-cover transition-transform duration-500 group-hover:scale-110">
                                            @else
                                                <div class="w-full h-32 flex items-center justify-center bg-gray-100">
                                                    <i class="fas fa-file-alt text-3xl text-gray-300"></i>
                                                </div>
                                            @endif

                                            <div class="absolute inset-0 bg-navy/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                                <a href="{{ asset('storage/' . $attachment) }}" target="_blank" 
                                                   class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-navy hover:bg-gold transition-all">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>
                                                <a href="{{ asset('storage/' . $attachment) }}" download
                                                   class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-navy hover:bg-gold transition-all">
                                                    <i class="fas fa-download text-sm"></i>
                                                </a>
                                            </div>

                                            <div class="p-3 bg-white/80 backdrop-blur-sm border-t border-gray-50">
                                                <span class="text-[10px] text-gray-400 font-mono font-bold truncate block">
                                                    {{ basename($attachment) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student & Info Column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Related Student -->
                <div class="bg-navy rounded-[2.5rem] p-8 shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-user-graduate text-8xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-black font-cairo mb-6 flex items-center gap-2">
                        <i class="fas fa-user-circle text-gold"></i>
                        <span>الطالب المعني</span>
                    </h3>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-gold font-black text-xl shadow-inner border border-white/5">
                            {{ mb_substr($violation->student->name_ar, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold font-almarai leading-tight">{{ $violation->student->name_ar }}</span>
                            <span class="text-xs font-mono font-bold opacity-60 tracking-widest mt-1">{{ $violation->student->student_number }}</span>
                        </div>
                    </div>

                    <a href="{{ route('students.show', $violation->student_id) }}" 
                       class="w-full py-4 bg-white/10 hover:bg-white text-white hover:text-navy rounded-2xl font-bold font-cairo transition-all flex items-center justify-center gap-2 border border-white/10">
                        <i class="fas fa-external-link-alt text-xs"></i>
                        <span>عرض ملف الطالب الإلكتروني</span>
                    </a>
                </div>

                <!-- Violation Info -->
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-50 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold font-cairo uppercase">تاريخ المخالفة</span>
                            <span class="text-navy font-black text-sm font-mono">{{ $violation->violation_date->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold font-cairo uppercase">سجلت بواسطة</span>
                            <span class="text-navy font-black text-sm font-cairo">{{ $violation->recordedBy->name ?? 'نظام مبرمج' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] text-gray-400 font-bold font-cairo uppercase">تاريخ التسجيل</span>
                            <span class="text-navy font-black text-xs font-mono">{{ $violation->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-50">
                        <form action="{{ route('violations.destroy', $violation->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المخالفة نهائياً؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-4 bg-red-50 text-red-500 rounded-2xl font-bold font-cairo hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt text-xs"></i>
                                <span>حذف من السجل</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
