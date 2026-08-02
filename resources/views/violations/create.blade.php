@extends('layouts.app')

@section('title', 'تسجيل مخالفة')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <a href="{{ route('administrative.index', ['tab' => 'violations']) }}" 
                   class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-navy font-cairo">تسجيل مخالفة جديدة</h1>
                    <p class="text-gray-400 font-almarai text-sm mt-1">يرجى تحري الدقة عند تسجيل المخالفات الانضباطية</p>
                </div>
            </div>

            <form action="{{ route('violations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                @if(request('circle_attendance_id'))
                    @if(is_array(request('circle_attendance_id')))
                        @foreach(request('circle_attendance_id') as $id)
                            <input type="hidden" name="circle_attendance_id[]" value="{{ $id }}">
                        @endforeach
                    @else
                        <input type="hidden" name="circle_attendance_id[]" value="{{ request('circle_attendance_id') }}">
                    @endif
                @endif

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Student Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo">اختيار الطلاب المعنيين <span class="text-red-500">*</span></label>
                            
                            <div x-data="{ 
                                search: '', 
                                selected: {{ json_encode(old('student_id', request('student_id') ? (is_array(request('student_id')) ? request('student_id') : [request('student_id')]) : [])) }}, 
                                items: @js($students->map(fn($s) => ['id' => $s->id, 'name' => $s->name_ar, 'num' => $s->student_number])),
                                get filteredItems() {
                                    if(this.search.length < 2) return [];
                                    return this.items.filter(i => 
                                        ((i.name && i.name.includes(this.search)) || (i.num && i.num.toString().includes(this.search))) && 
                                        !this.selected.includes(i.id)
                                    ).slice(0, 5)
                                },
                                addStudent(student) {
                                    if(!this.selected.includes(student.id)) {
                                        this.selected.push(student.id);
                                    }
                                    this.search = '';
                                },
                                removeStudent(id) {
                                    this.selected = this.selected.filter(i => i != id);
                                }
                            }" class="relative">
                                
                                <!-- Selected Badges -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <template x-for="id in selected" :key="id">
                                        <div class="bg-navy/5 text-navy px-4 py-2 rounded-2xl flex items-center gap-3 border border-navy/10 animate-fade-in">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold font-almarai" x-text="items.find(i => i.id == id)?.name"></span>
                                                <span class="text-[10px] opacity-50 font-mono" x-text="items.find(i => i.id == id)?.num"></span>
                                            </div>
                                            <button type="button" @click="removeStudent(id)" class="w-6 h-6 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                                <i class="fas fa-times text-[10px]"></i>
                                            </button>
                                            <input type="hidden" name="student_id[]" :value="id">
                                        </div>
                                    </template>
                                    
                                    <template x-if="selected.length === 0">
                                        <div class="text-gray-300 text-xs font-almarai italic py-2">لم يتم اختيار أي طالب بعد...</div>
                                    </template>
                                </div>

                                <!-- Search Input -->
                                <div class="relative">
                                    <input type="text" x-model="search" 
                                           placeholder="ابحث باسم الطالب أو الرقم الجامعي (اكتب حرفين على الأقل)..." 
                                           class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right">
                                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300">
                                        <i class="fas fa-search text-xl"></i>
                                    </div>
                                </div>

                                <!-- Results Dropdown -->
                                <div x-show="search.length >= 2" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     class="absolute z-50 w-full mt-3 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                                    
                                    <div class="p-2">
                                        <template x-for="student in filteredItems" :key="student.id">
                                            <button type="button" 
                                                    @click="addStudent(student)"
                                                    class="w-full text-right px-6 py-4 rounded-2xl hover:bg-navy/5 flex items-center justify-between group transition-all">
                                                <div class="flex flex-col">
                                                    <span class="font-bold text-navy font-almarai group-hover:text-navy" x-text="student.name"></span>
                                                    <span class="text-[10px] text-gray-400 font-mono" x-text="student.num"></span>
                                                </div>
                                                <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 group-hover:bg-navy group-hover:text-white transition-all">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </div>
                                            </button>
                                        </template>
                                        
                                        <div x-show="filteredItems.length === 0" class="p-8 text-center">
                                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i class="fas fa-user-slash text-gray-300"></i>
                                            </div>
                                            <p class="text-gray-400 font-almarai text-xs">لا توجد نتائج مطابقة أو الطلاب مختارون مسبقاً</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('student_id') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                        <!-- Violation Type -->
                        <div>
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">نوع المخالفة <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="text" name="type" value="{{ old('type') }}" required list="violation_types"
                                       placeholder="مثلاً: غياب غير مبرر، سوء سلوك..."
                                       class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right">
                                <datalist id="violation_types">
                                    <option value="غياب غير مبرر">
                                    <option value="إثارة فوضى">
                                    <option value="إتلاف ممتلكات">
                                    <option value="استخدام أجهزة محظورة">
                                    <option value="مخالفة تعليمات الأمن">
                                    <option value="تأخر عن الموعد">
                                </datalist>
                            </div>
                            @error('type') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                        <!-- Severity -->
                        <div x-data="{ severity: '{{ old('severity', 'minor') }}' }">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">مستوى الجسامة <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-3">
                                @foreach([
                                    'minor' => ['label' => 'بسيطة', 'color' => 'blue', 'icon' => 'fa-info-circle'], 
                                    'moderate' => ['label' => 'متوسطة', 'color' => 'orange', 'icon' => 'fa-exclamation-circle'], 
                                    'severe' => ['label' => 'جسيمة', 'color' => 'red', 'icon' => 'fa-exclamation-triangle']
                                ] as $val => $data)
                                    <label class="cursor-pointer group relative">
                                        <input type="radio" name="severity" value="{{ $val }}" class="sr-only" 
                                               x-model="severity" required>
                                        <div class="px-3 py-5 rounded-2xl border-2 transition-all p-4 text-center"
                                             :class="{
                                                'bg-blue-50 border-blue-500 shadow-inner': severity === 'minor' && '{{ $val }}' === 'minor',
                                                'bg-orange-50 border-orange-500 shadow-inner': severity === 'moderate' && '{{ $val }}' === 'moderate',
                                                'bg-red-50 border-red-500 shadow-inner': severity === 'severe' && '{{ $val }}' === 'severe',
                                                'bg-gray-50 border-gray-100': severity !== '{{ $val }}'
                                             }">
                                            <i class="fas {{ $data['icon'] }} mb-2 block text-2xl"
                                               :class="{
                                                   'text-blue-600': severity === 'minor' && '{{ $val }}' === 'minor',
                                                   'text-orange-600': severity === 'moderate' && '{{ $val }}' === 'moderate',
                                                   'text-red-600': severity === 'severe' && '{{ $val }}' === 'severe',
                                                   'text-gray-300': severity !== '{{ $val }}'
                                               }"></i>
                                            <span class="block font-black font-cairo text-sm"
                                                  :class="{
                                                      'text-blue-700': severity === 'minor' && '{{ $val }}' === 'minor',
                                                      'text-orange-700': severity === 'moderate' && '{{ $val }}' === 'moderate',
                                                      'text-red-700': severity === 'severe' && '{{ $val }}' === 'severe',
                                                      'text-gray-400': severity !== '{{ $val }}'
                                                  }">{{ $data['label'] }}</span>
                                        </div>
                                        
                                        <!-- Checkmark Icon -->
                                        <div class="absolute -top-2 -right-2 w-7 h-7 bg-white rounded-full shadow-lg flex items-center justify-center border-2"
                                             x-show="severity === '{{ $val }}'"
                                             :class="{
                                                 'border-blue-500 text-blue-500': '{{ $val }}' === 'minor',
                                                 'border-orange-500 text-orange-500': '{{ $val }}' === 'moderate',
                                                 'border-red-500 text-red-500': '{{ $val }}' === 'severe'
                                             }">
                                            <i class="fas fa-check-circle text-sm"></i>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('severity') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">تاريخ المخالفة <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="date" name="violation_date" value="{{ old('violation_date', date('Y-m-d')) }}" required
                                       class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right">
                            </div>
                            @error('violation_date') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                        <!-- Attachments Mockup (optional as per controller support) -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">المرفقات والوثائق</label>
                            <div class="relative">
                                <input type="file" name="attachments[]" multiple class="hidden" id="attachments_input" onchange="updateFilesList(this)">
                                <label for="attachments_input" class="w-full px-6 py-5 rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50/50 flex items-center justify-between cursor-pointer hover:border-navy transition-all group">
                                    <span id="file-count" class="text-gray-400 font-almarai text-sm">ارفع صور أو محاضر (اختياري)...</span>
                                    <i class="fas fa-paperclip text-gray-200 group-hover:text-navy transition-all"></i>
                                </label>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">تفاصيل مسببات المخالفة <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="5" required
                                      placeholder="قم بوصف الواقعة بالتفصيل مع ذكر أي ملاحظات إضافية..."
                                      class="w-full px-8 py-6 rounded-[2rem] border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right leading-relaxed">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" 
                            class="flex-1 bg-red-600 text-white font-black font-cairo py-6 rounded-[2.5rem] shadow-2xl shadow-red-600/20 hover:bg-red-700 hover:-translate-y-1 transition-all flex items-center justify-center gap-4 text-xl">
                        <i class="fas fa-check-double"></i>
                        <span>اعتماد وتسجيل المخالفة</span>
                    </button>
                    <a href="{{ route('administrative.index', ['tab' => 'violations']) }}" 
                       class="px-16 py-6 bg-white text-gray-400 font-black font-cairo rounded-[2.5rem] border border-gray-100 hover:bg-gray-50 transition-all text-lg">
                        تراجع
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function updateFilesList(input) {
        const label = document.getElementById('file-count');
        if(input.files.length > 0) {
            label.textContent = `تم اختيار ${input.files.length} ملفات`;
            label.classList.add('text-navy', 'font-bold');
        } else {
            label.textContent = 'ارفع صور أو محاضر (اختياري)...';
            label.classList.remove('text-navy', 'font-bold');
        }
    }
</script>
@endpush
