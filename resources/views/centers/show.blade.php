@extends('layouts.app')



@section('title', 'تفاصيل المركز - ' . $center->name)

@section('content')
    <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-gray-100 shrink-0">
                <img src="{{ $center->logo ? asset('storage/' . $center->logo) : 'https://ui-avatars.com/api/?name=' . urlencode($center->name) . '&background=0f172a&color=fff' }}" 
                     alt="{{ $center->name }}" class="w-full h-full object-cover">
            </div>
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo lowercase">{{ $center->name }}</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-gold"></i> {{ $center->address }}
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('centers.edit', $center) }}" 
               class="px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy hover:text-white shadow-sm font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-edit"></i>
                <span>تعديل المركز</span>
            </a>
            <a href="{{ route('centers.index') }}" 
               class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 font-cairo font-bold transition-all flex items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Stats Cards -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-gold transition-colors">
            <div class="w-16 h-16 bg-navy/5 text-navy rounded-2xl flex items-center justify-center mb-4 group-hover:bg-navy group-hover:text-gold transition-all">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <h3 class="text-gray-400 font-bold font-cairo mb-1">إجمالي الساكنين</h3>
            <p class="text-4xl font-black text-navy">{{ $center->residents_count }}</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-gold transition-colors">
            <div class="w-16 h-16 bg-gold/10 text-gold rounded-2xl flex items-center justify-center mb-4 group-hover:bg-navy transition-all">
                <i class="fas fa-door-open text-2xl"></i>
            </div>
            <h3 class="text-gray-400 font-bold font-cairo mb-1">عدد الغرف السكنية</h3>
            <p class="text-4xl font-black text-navy">{{ $center->rooms_count }}</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center text-center group hover:border-gold transition-colors">
            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-navy transition-all">
                <i class="fas fa-users-cog text-2xl"></i>
            </div>
            <h3 class="text-gray-400 font-bold font-cairo mb-1">الطاقم الإداري</h3>
            <p class="text-4xl font-black text-navy">{{ $center->staff_count }}</p>
        </div>

        <!-- Details Column -->
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-navy font-cairo mb-6 flex items-center gap-3">
                    <i class="fas fa-info-circle text-gold"></i> بيانات التواصل والتموقع
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center shrink-0 border border-gray-100">
                                <i class="fas fa-envelope text-navy/50"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold font-cairo mb-1">البريد الإلكتروني الرسمي</p>
                                <p class="text-gray-800 font-bold font-almarai italic">{{ $center->email ?? 'غير متوفر' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center shrink-0 border border-gray-100">
                                <i class="fas fa-phone text-navy/50"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold font-cairo mb-1">رقم الهاتف</p>
                                <p class="text-gray-800 font-bold font-almarai" dir="ltr">{{ $center->phone ?? 'غير متوفر' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 flex flex-col justify-center border border-gray-100">
                        <p class="text-xs text-gray-400 font-bold font-cairo mb-2 text-center uppercase tracking-widest">حالة المركز الحالية</p>
                        <div class="flex items-center justify-center gap-3 px-6 py-4 bg-white rounded-xl shadow-sm border-2 {{ $center->is_active ? 'border-green-100 text-green-600' : 'border-red-100 text-red-600' }}">
                            <i class="fas {{ $center->is_active ? 'fa-check-circle' : 'fa-times-circle' }} text-2xl"></i>
                            <span class="text-xl font-black font-cairo">{{ $center->is_active ? 'مركز نشط ومفعل' : 'مركز معطل حالياً' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div>
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 space-y-4">
                <h3 class="text-lg font-bold text-navy font-cairo mb-4 border-b border-gray-50 pb-4">إجراءات سريعة</h3>
                
                <a href="{{ route('centers.export-pdf', $center) }}" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gold/10 hover:text-navy rounded-2xl transition-all group font-almarai">
                    <span class="font-bold">تصدير إحصائيات المركز</span>
                    <i class="fas fa-file-pdf opacity-50 group-hover:opacity-100"></i>
                </a>
                <a href="{{route('admin.users.index', ['center_id' => $center->id])}}" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gold/10 hover:text-navy rounded-2xl transition-all group font-almarai">
                  <span class="font-bold">طاقم عمل المركز</span>
                    <i class="fas fa-users-cog opacity-50 group-hover:opacity-100"></i>
                </a>

                {{-- <a href="{{ route('managers.index', ['center_id' => $center->id]) }}" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gold/10 hover:text-navy rounded-2xl transition-all group font-almarai">
                    <span class="font-bold">عرض مدراء المركز</span>
                    <i class="fas fa-users-cog opacity-50 group-hover:opacity-100"></i>
                </a> --}}

                {{-- <form action="{{ route('centers.toggle-status', $center) }}" method="POST"
                    onsubmit="return confirm('{{ $center->is_active ? 'هل أنت متأكد من إيقاف المركز؟' : 'هل تريد تفعيل المركز؟' }}')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full flex items-center justify-between p-4 rounded-2xl transition-all group font-almarai {{ $center->is_active ? 'bg-red-50 text-red-600 hover:bg-red-600 hover:text-white' : 'bg-green-50 text-green-700 hover:bg-green-600 hover:text-white' }}">
                        <span class="font-bold">{{ $center->is_active ? 'إيقاف المركز' : 'تفعيل المركز' }}</span>
                        <i class="fas fa-power-off opacity-50 group-hover:opacity-100 transition-opacity"></i>
                    </button>
                </form> --}}
            </div>
        </div>
    </div>
@endsection
