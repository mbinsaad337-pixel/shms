@extends('layouts.app')

@section('title', 'إدارة أصول المركز')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8 bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h2 class="text-3xl font-bold text-navy font-cairo">سجل الأصول والعهدة العينية</h2>
                <p class="text-gray-500 font-almarai mt-1">تتبع التجهيزات، الأثاث، والأجهزة التقنية في المركز</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('assets.export-list', request()->all()) }}" target="_blank"
                    class="px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy/5 font-cairo font-bold transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i>
                    <span>طباعة</span>
                </a>
                @if(auth()->user()->hasRole('super-admin'))
                    <form action="{{ route('assets.index') }}" method="GET" class="flex items-center gap-2">
                        <select name="center_id" onchange="this.form.submit()" 
                            class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-gold outline-none text-sm font-cairo">
                            <option value="">كل المراكز</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                @if(!auth()->user()->hasRole('super-admin'))
                <a href="{{ route('assets.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1">
                    + تسجيل أصل جديد
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($assets as $asset)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover group">
                    <div class="h-48 bg-gray-100 relative overflow-hidden">
                        @if($asset->photo)
                            <img src="{{ asset('storage/' . $asset->photo) }}"
                                class="w-full h-full object-cover transition-transform group-hover:scale-110"
                                alt="{{ $asset->name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="h-20 w-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="absolute top-4 right-4">
                            <span
                                class="px-3 py-1 rounded-full text-[10px] font-bold shadow-sm {{ $asset->status == 'good' ? 'bg-green-500 text-white' : ($asset->status == 'needs_maintenance' ? 'bg-orange-500 text-white' : 'bg-red-500 text-white') }}">
                                {{ $asset->status == 'good' ? 'حالة جيدة' : ($asset->status == 'needs_maintenance' ? 'يحتاج صيانة' : 'تالف') }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800 font-almarai">{{ $asset->name }}</h3>
                            <span class="text-xs font-bold text-primary font-cairo">{{ $asset->code }}</span>
                        </div>
                        <p class="text-xs text-gray-400 font-almarai mb-4">{{ $asset->category }} - {{ $asset->type }}</p>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <p class="text-[10px] text-gray-400 font-cairo">القيمة التقديرية</p>
                                <p class="text-sm font-bold text-gray-700 font-almarai">{{ number_format($asset->value, 2) }}
                                    ر.ي</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-xl">
                                <p class="text-[10px] text-gray-400 font-cairo">تاريخ التسجيل</p>
                                <p class="text-sm font-bold text-gray-700 font-almarai">
                                    {{ $asset->created_at->format('Y/m/d') }}</p>
                            </div>
                        </div>

                        @if(!auth()->user()->hasRole('super-admin'))
                        <div class="flex gap-2">
                            <a href="{{ route('assets.edit', $asset) }}"
                                class="flex-1 text-center py-2 bg-navy text-white rounded-lg text-sm font-cairo font-bold hover:bg-navy/80 transition-colors">تعديل
                                البيانات</a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST"
                                data-confirm="هل أنت متأكد من حذف هذا الأصل؟لا يمكن التراجع عن هذا الإجراء.">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-gray-300 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
