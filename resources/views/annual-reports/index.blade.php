@extends('layouts.app')

@section('title', 'التقارير السنوية')

@section('content')
<div x-data="{
    showModal: false,
    title: '',
    year: {{ date('Y') }},
    uploading: false,
    resetForm() {
        this.title = '';
        this.year = {{ date('Y') }};
    }
}">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-navy-900 font-cairo flex items-center gap-3">
                <i class="fas fa-file-alt text-gold"></i>
                التقارير السنوية
            </h1>
            <p class="text-gray-500 text-sm mt-1 font-almarai">إدارة ورفع تقارير المركز السنوية</p>
        </div>
        <button @click="showModal = true" class="bg-gold hover:bg-gold/90 text-white font-bold py-3 px-6 rounded-xl transition-all flex items-center gap-2 shadow-lg shadow-gold/20">
            <i class="fas fa-plus"></i>
            رفع تقرير جديد
        </button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Reports Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($reports->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-file-alt text-6xl text-gray-200 mb-4"></i>
                <h3 class="text-lg font-bold text-gray-400 font-cairo">لا توجد تقارير سنوية</h3>
                <p class="text-gray-400 text-sm mt-2 font-almarai">ابدأ برفع التقارير السنوية للمركز</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">#</th>
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">عنوان التقرير</th>
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">السنة</th>
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">الملف</th>
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">الحجم</th>
                            <th class="text-right px-6 py-4 text-sm font-bold text-gray-600 font-cairo">تاريخ الرفع</th>
                            <th class="text-center px-6 py-4 text-sm font-bold text-gray-600 font-cairo">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($reports as $report)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500 font-almarai">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-navy-900/5 flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-red-500"></i>
                                        </div>
                                        <span class="font-bold text-navy-900 font-cairo">{{ $report->title }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-gold/10 text-gold">
                                        {{ $report->year }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-almarai">{{ $report->file_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-almarai">{{ $report->file_size_formatted }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-almarai">{{ $report->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('annual-reports.download', $report) }}" class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition-colors" title="تحميل">
                                            <i class="fas fa-download text-sm"></i>
                                        </a>
                                        <form action="{{ route('annual-reports.destroy', $report) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100 transition-colors" title="حذف">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Upload Modal --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         @click.self="showModal = false">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" @click.stop>
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-navy-900 font-cairo flex items-center gap-2">
                    <i class="fas fa-upload text-gold"></i>
                    رفع تقرير جديد
                </h3>
                <button @click="showModal = false; resetForm()" class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times text-gray-500"></i>
                </button>
            </div>
            <form action="{{ route('annual-reports.store') }}" method="POST" enctype="multipart/form-data" @submit="uploading = true">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">عنوان التقرير</label>
                        <input type="text" name="title" x-model="title" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all font-almarai"
                               placeholder="مثال: التقرير السنوي للنشاط الطلابي">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">السنة</label>
                        <input type="number" name="year" x-model="year" required min="2000" max="{{ date('Y') + 1 }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all font-almarai">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo">الملف</label>
                        <div class="relative">
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all font-almarai file:ml-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gold/10 file:text-gold file:font-bold file:font-cairo hover:file:bg-gold/20">
                        </div>
                        <p class="text-xs text-gray-400 mt-2 font-almarai">الصيغ المدعومة: PDF, Word, Excel (حد أقصى 20 ميجابايت)</p>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <button type="button" @click="showModal = false; resetForm()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors font-cairo">
                        إلغاء
                    </button>
                    <button type="submit" :disabled="uploading"
                            class="px-5 py-2.5 rounded-xl bg-gold text-white font-bold hover:bg-gold/90 transition-colors disabled:opacity-50 font-cairo flex items-center gap-2">
                        <template x-if="uploading">
                            <i class="fas fa-spinner fa-spin"></i>
                        </template>
                        <span x-text="uploading ? 'جاري الرفع...' : 'رفع التقرير'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
