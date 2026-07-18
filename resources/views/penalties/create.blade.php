@extends('layouts.app')

@section('title', 'إسناد عقوبة')

@section('content')
    <div class="container mx-auto px-6 py-8" x-data="{ 
        search: '', 
        selectedStudents: {{ old('student_id') ? json_encode(is_array(old('student_id')) ? old('student_id') : [old('student_id')]) : '[]' }}, 
        students: @js($students->map(fn($s) => ['id' => $s->id, 'name' => $s->name_ar, 'num' => $s->student_number])),
        allViolations: @js($violations->map(fn($v) => ['id' => $v->id, 'student_id' => $v->student_id, 'text' => $v->student->name_ar . ' - ' . $v->type . ' (' . $v->violation_date->format('Y/m/d') . ')'])),
        get filteredStudents() {
            if(this.search.length < 2) return [];
            return this.students.filter(i => 
                (i.name.includes(this.search) || i.num.toString().includes(this.search)) && 
                !this.selectedStudents.includes(i.id)
            ).slice(0, 5)
        },
        get filteredViolations() {
            if(this.selectedStudents.length === 0) return this.allViolations.slice(0, 15);
            return this.allViolations.filter(v => this.selectedStudents.includes(v.student_id));
        },
        addStudent(studentId) {
            if(!this.selectedStudents.includes(studentId)) {
                this.selectedStudents.push(studentId);
            }
            this.search = '';
        },
        removeStudent(studentId) {
            this.selectedStudents = this.selectedStudents.filter(id => id != studentId);
        }
    }">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8 flex items-center gap-4">
                <a href="{{ route('penalties.index') }}" 
                   class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-navy font-cairo">إسناد عقوبة انضباطية</h1>
                    <p class="text-gray-400 font-almarai text-sm mt-1">تطبيق جزاء انضباطي على طالب أو أكثر (جماعي)</p>
                </div>
            </div>

            <form action="{{ route('penalties.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Multi-Student Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">الطلاب المستحقون للعقوبة <span class="text-red-500">*</span></label>
                            
                            <div class="relative">
                                <!-- Selected Student Badges -->
                                <div class="flex flex-wrap gap-2 mb-4 min-h-[50px] p-2 bg-gray-50/30 rounded-2xl border border-dashed border-gray-100">
                                    <template x-for="id in selectedStudents" :key="id">
                                        <div class="bg-navy text-white px-4 py-2 rounded-xl flex items-center gap-3 border border-navy shadow-sm animate-fade-in group">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold font-almarai" x-text="students.find(s => s.id == id)?.name"></span>
                                                <span class="text-[9px] opacity-70 font-mono" x-text="students.find(s => s.id == id)?.num"></span>
                                            </div>
                                            <button type="button" @click="removeStudent(id)" class="w-5 h-5 bg-navy-light/20 rounded-full flex items-center justify-center hover:bg-red-500 transition-all">
                                                <i class="fas fa-times text-[8px]"></i>
                                            </button>
                                            <input type="hidden" name="student_id[]" :value="id">
                                        </div>
                                    </template>
                                    <template x-if="selectedStudents.length === 0">
                                        <span class="text-gray-300 text-xs italic font-almarai p-2">لم يتم اختيار أي طالب بعد...</span>
                                    </template>
                                </div>

                                <!-- Search Field -->
                                <div class="relative">
                                    <input type="text" x-model="search" 
                                           placeholder="ابحث باسم الطالب أو الرقم الجامعي..." 
                                           class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right">
                                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300">
                                        <i class="fas fa-user-plus text-xl"></i>
                                    </div>
                                </div>

                                <!-- Search Results -->
                                <div x-show="search.length >= 2" 
                                     class="absolute z-50 w-full mt-3 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                                    <div class="p-2">
                                        <template x-for="student in filteredStudents" :key="student.id">
                                            <button type="button" 
                                                    @click="addStudent(student.id)"
                                                    class="w-full text-right px-6 py-4 rounded-2xl hover:bg-navy text-navy hover:text-white flex items-center justify-between group transition-all">
                                                <div class="flex flex-col">
                                                    <span class="font-bold font-almarai" x-text="student.name"></span>
                                                    <span class="text-[10px] opacity-70 font-mono" x-text="student.num"></span>
                                                </div>
                                                <i class="fas fa-plus text-xs opacity-0 group-hover:opacity-100 transition-all"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            @error('student_id') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                        <!-- Related Violation (Filtered) -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">المخالفة المرتبطة (اختياري)</label>
                            <div class="relative">
                                <select name="violation_id" class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right appearance-none">
                                    <template x-if="filteredViolations.length === 0">
                                        <option value="">-- لا توجد مخالفات مسجلة للطلاب المختارين --</option>
                                    </template>
                                    <template x-if="filteredViolations.length > 0">
                                        <option value="">-- اختر المخالفة (اختياري) --</option>
                                    </template>
                                    <template x-for="v in filteredViolations" :key="v.id">
                                        <option :value="v.id" x-text="v.text"></option>
                                    </template>
                                </select>
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Penalty Type -->
                        <div class="md:col-span-1">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">نوع العقوبة <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select name="type" required class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right appearance-none">
                                    <option value="">-- اختر نوع العقوبة --</option>
                                    <option value="verbal_warning">تنبيه شفوي</option>
                                    <option value="written_warning">إنذار كتابي</option>
                                    <option value="service_suspension">عقوبة خدمات (تحميل/صيانة)</option>
                                    <option value="temporary_suspension">فصل مؤقت من السكن</option>
                                    <option value="expulsion">فصل نهائي</option>
                                </select>
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                    <i class="fas fa-gavel"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div>
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">تاريخ البدء</label>
                            <input type="date" name="start_date" value="{{ date('Y-m-d') }}"
                                   class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-mono text-right transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">تاريخ الانتهاء (اختياري)</label>
                            <input type="date" name="end_date"
                                   class="w-full px-6 py-5 rounded-2xl border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-mono text-right transition-all">
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-black text-navy mb-4 font-cairo text-right">وصف ومبررات العقوبة (اختياري)</label>
                            <textarea name="description" rows="5"
                                      placeholder="يمكنك كتابة مبررات العقوبة هنا في حال عدم ربطها بمخالفة..."
                                      class="w-full px-8 py-6 rounded-[2rem] border border-gray-100 bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-navy/5 focus:border-navy outline-none font-almarai transition-all text-right leading-relaxed">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-xs mt-3 font-almarai">{{ $message }}</p> @enderror
                        </div>

                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" 
                            class="flex-1 bg-orange-600 text-white font-black font-cairo py-6 rounded-[2.5rem] shadow-2xl shadow-orange-600/20 hover:bg-orange-700 hover:-translate-y-1 transition-all flex items-center justify-center gap-4 text-xl">
                        <i class="fas fa-check-double"></i>
                        <span>اعتماد وإسناد الجزاء</span>
                    </button>
                    <a href="{{ route('penalties.index') }}" 
                       class="px-16 py-6 bg-white text-gray-400 font-black font-cairo rounded-[2.5rem] border border-gray-100 hover:bg-gray-50 transition-all text-lg">
                        تراجع
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
