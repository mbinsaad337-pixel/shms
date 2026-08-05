@extends('layouts.app')

@section('title', 'إدارة الأندية الطلابية')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8 flex justify-between items-center bg-white p-8 rounded-3xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">الأندية الطلابية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-2">إدارة الأندية التخصصية والأنشطة الطلابية المستمرة</p>
            </div>
            <div class="flex gap-4">
                
                <a href="{{ route('activities.index') }}"
                    class="px-6 py-4 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>سجل الفعاليات</span>
                </a>
                @if (!auth()->user()->hasRole('super-admin'))
                    <a href="{{ route('clubs.export-list', request()->all()) }}" target="_blank"
                    class="px-6 py-4 bg-white text-navy border-2 border-navy/10 rounded-2xl hover:bg-navy/5 font-cairo font-bold transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-file-pdf"></i>
                    <span>تصدير Pdf</span>
                    </a>
                    <button onclick="openClubModal()"
                        class="px-8 py-4 bg-navy text-white rounded-2xl hover:bg-navy/90 shadow-xl font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-3 group">
                        <i class="fas fa-plus-circle text-gold group-hover:rotate-90 transition-transform"></i>
                        <span>إنشاء نادٍ جديد</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Filters Section -->
        @if (isset($centers) && count($centers) > 0)
            <div class="bg-white p-6 rounded-3xl shadow-sm mb-8 border border-gray-100">
                <form action="{{ route('clubs.index') }}" method="GET" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-400 mb-2 uppercase font-cairo">المركز /
                            السكن</label>
                        <select name="center_id" onchange="this.form.submit()"
                            class="w-full bg-gray-50 border-0 rounded-xl p-3 text-sm focus:ring-2 focus:ring-gold/20 font-almarai">
                            <option value="">جميع المراكز</option>
                            @foreach ($centers as $center)
                                <option value="{{ $center->id }}"
                                    {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                    {{ $center->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        @if (request()->filled('center_id'))
                            <a href="{{ route('clubs.index') }}"
                                class="w-full flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all border border-red-100 p-3"
                                title="إعادة تعيين">
                                <i class="fas fa-times ml-2"></i> إلغاء
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        @endif

        <!-- Clubs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if (is_countable($clubs) ? count($clubs) > 0 : (method_exists($clubs, 'count') ? $clubs->count() > 0 : !empty($clubs)))
                @foreach ($clubs as $club)
                    <div
                        class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden card-premium group h-full flex flex-col">
                        <div class="relative h-48 bg-navy/5 overflow-hidden flex items-center justify-center p-8">
                            @if ($club->logo)
                                <img src="{{ asset('storage/' . $club->logo) }}" alt="Club Logo"
                                    class="w-24 h-24 object-contain opacity-80 group-hover:scale-110 transition-transform duration-500">
                            @else
                                <i
                                    class="fas fa-users-rectangle text-6xl text-navy/10 group-hover:scale-110 transition-transform duration-500"></i>
                            @endif

                            <div class="absolute top-6 left-6">
                                <span
                                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700">
                                    {{ $club->status == 'active' ? 'نشط' : 'معطل' }}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 flex-grow">
                            <div class="mb-6">
                                <span
                                    class="text-gold font-bold text-xs uppercase font-cairo block mb-1 tracking-wider">{{ $club->category }}</span>
                                <h2 class="text-2xl font-black text-navy font-cairo leading-tight">{{ $club->name }}</h2>
                            </div>

                            <p class="text-gray-500 font-almarai text-sm leading-relaxed mb-8 line-clamp-2">
                                {{ $club->description }}
                            </p>

                            <div class="flex items-center justify-between mt-auto border-t border-gray-50 pt-6">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase font-cairo tracking-widest">عدد
                                        الأعضاء</span>
                                    <span class="text-xl font-black text-navy">{{ $club->members_count }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('clubs.show', $club->id) }}"
                                        class="w-10 h-10 bg-navy/5 text-navy rounded-xl flex items-center justify-center hover:bg-navy hover:text-yellow transition-all"
                                        title="عرض التفاصيل">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('clubs.export-pdf', $club->id) }}" target="_blank"
                                        class="w-10 h-10 bg-gold/10 text-gold rounded-xl flex items-center justify-center hover:bg-gold hover:text-yellow transition-all"
                                        title="تصدير PDF">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </a>
                                    @if (!auth()->user()->hasRole('super-admin'))
                                        <a href="{{ route('clubs.edit', $club->id) }}"
                                            class="w-10 h-10 bg-gold/5 text-gold rounded-xl flex items-center justify-center hover:bg-gold hover:text-yellow transition-all"
                                            title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('clubs.destroy', $club->id) }}" method="POST"
                                            data-confirm="هل أنت متأكد من حذف هذا النادي؟" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div
                    class="col-span-full bg-white rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-100 shadow-sm">
                    <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-university text-4xl text-gray-200"></i>
                    </div>
                    <h3 class="text-2xl font-black text-navy font-cairo">لا توجد أندية حالياً</h3>
                    <p class="text-gray-400 font-almarai mt-2">ابدأ بإنشاء الأندية الطلابية لتنظيم الأنشطة بشكل احترافي</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Club Modal -->
    <div id="clubModal"
        class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden items-start justify-center z-[1000] p-4 overflow-y-auto">
        <div
            class="bg-white rounded-[2.5rem] p-10 max-w-xl w-full shadow-2xl transform transition-all border-t-8 border-gold my-8 relative max-h-none">
            <div class="flex items-center gap-5 mb-10 border-b border-gray-50 pb-6">
                <div class="w-16 h-16 bg-gold/10 rounded-2xl flex items-center justify-center text-gold shadow-sm">
                    <i class="fas fa-plus-circle text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black font-cairo text-navy">إنشاء نادٍ طابي جديد</h2>
                    <p class="text-gray-400 font-almarai text-sm italic">حدد تخصص النادي وهدفه الأساسي</p>
                </div>
            </div>

            <form action="{{ route('clubs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">شعار النادي
                            (اختياري)</label>
                        <input type="file" name="logo" accept="image/*"
                            class="w-full px-5 py-3 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none transition-all font-almarai text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">اسم النادي</label>
                        <input type="text" name="name" required placeholder="مثال: نادي الحاسب الآلي"
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none text-right transition-all font-almarai">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">تصنيف النادي</label>
                        <select name="category" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai">
                            <option value="ثقافي">ثقافي</option>
                            <option value="رياضي">رياضي</option>
                            <option value="تقني">تقني</option>
                            <option value="فني">فني</option>
                            <option value="اجتماعي">اجتماعي</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-navy mb-3 font-cairo text-right">وصف النادي</label>
                        <textarea name="description" rows="4" required placeholder="تحدث عن رؤية النادي وأهدافه..."
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 outline-none text-right transition-all font-almarai resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-4 pt-8">
                    <button type="submit"
                        class="flex-[3] bg-navy text-white py-5 rounded-2xl font-black text-lg shadow-xl hover:shadow-navy/40 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-check-circle text-gold"></i>
                        <span>حفظ بيانات النادي</span>
                    </button>
                    <button type="button" onclick="closeClubModal()"
                        class="flex-1 bg-gray-100 text-gray-400 py-5 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openClubModal() {
            showModal('clubModal');
        }

        function closeClubModal() {
            hideModal('clubModal');
        }

        function showModal(id) {
            const m = document.getElementById(id);
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    </script>
@endsection
