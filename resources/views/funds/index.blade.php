@extends('layouts.app')

@section('title', 'إدارة الصناديق المالية')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 font-cairo">الصناديق المالية</h1>
                <p class="text-gray-600 font-almarai mt-2">إدارة الحسابات والصناديق التابعة للمركز</p>
            </div>
            @if(auth()->user()->hasRole('super-admin'))
            <button onclick="openCreateModal()"
                class="btn-primary flex items-center gap-2 px-6 py-3 rounded-2xl shadow-lg transform hover:-translate-y-1 transition-all">
                <i class="fas fa-plus"></i>
                <span>إضافة صندوق جديد</span>
            </button>
            @endif
        </div>

        @if(auth()->user()->hasRole('super-admin'))
        <form action="{{ route('funds.index') }}" method="GET" class="mb-8 flex flex-wrap items-end gap-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
            <div>
                <label class="mb-1 block text-xs font-bold text-gray-500 font-cairo text-right">اسم الصندوق</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم..."
                    class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-cairo outline-none focus:border-gold focus:bg-white text-right">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-gray-500 font-cairo text-right">المركز</label>
                <select name="center_id" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-cairo outline-none focus:border-gold focus:bg-white min-w-[150px]">
                    <option value="">كل المراكز</option>
                    @foreach($centers as $center)
                        <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-xl bg-navy px-5 py-2.5 text-sm font-bold text-white transition-colors hover:bg-navy/90 font-cairo mr-auto">
                <i class="fas fa-filter ml-1"></i> تصفية
            </button>
            @if(request()->filled('search') || request()->filled('center_id'))
                <a href="{{ route('funds.index') }}" class="px-3 py-2.5 text-sm font-bold text-gray-500 hover:text-navy font-cairo">إلغاء الفلترة</a>
            @endif
        </form>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @if(is_countable($funds) ? count($funds) > 0 : (method_exists($funds, 'count') ? $funds->count() > 0 : !empty($funds)))
    @foreach($funds as $fund)
                <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 card-hover relative group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-primary shadow-sm">
                            <i class="fas fa-vault text-2xl"></i>
                        </div>
                        <div class="flex gap-3">
                            @if(auth()->user()->hasRole('super-admin'))
                            <button onclick="openEditModal({{ json_encode($fund) }})"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl font-bold font-cairo transition-all text-sm">
                                <i class="fas fa-edit"></i>
                                <span>تعديل</span>
                            </button>
                            @if(!$fund->is_system)
                                <form action="{{ route('funds.destroy', $fund) }}" method="POST"
                                    data-confirm="هل أنت متأكد من حذف هذا الصندوق؟ لا يمكن التراجع عن هذا الإجراء.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl font-bold font-cairo transition-all text-sm">
                                        <i class="fas fa-trash"></i>
                                        <span>حذف</span>
                                    </button>
                                </form>
                            @endif
                            @endif
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-800 font-cairo mb-2">{{ $fund->name }}</h3>
                        <p class="text-gray-500 text-sm font-almarai leading-relaxed">
                            {{ $fund->description ?? 'لا يوجد وصف مضاف لهذا الصندوق' }}</p>
                    </div>

                    <div class="mt-auto pt-6 border-t border-gray-50 flex justify-between items-end">
                        <div>
                            <p class="text-gray-400 text-xs font-almarai mb-1">الرصيد المتوفر</p>
                            <p class="text-3xl font-black text-primary tracking-tight">
                                {{ number_format($fund->balance, 2) }}
                                <span class="text-sm font-normal text-gray-400 mr-1">ر.ي</span>
                            </p>
                        </div>
                        @if(auth()->user()->hasRole('super-admin'))
                            <div class="flex items-center gap-2 bg-blue-50 px-3 py-1.5 rounded-xl">
                                <i class="fas fa-building text-blue-400 text-xs"></i>
                                <span class="text-xs text-blue-700 font-bold">{{ $fund->center->name ?? 'غير محدد' }}</span>
                            </div>
                        @endif
                        @if($fund->is_system)
                            <span
                                class="bg-gray-100 text-gray-500 text-[10px] px-3 py-1.5 rounded-full font-bold uppercase tracking-wider">نظام</span>
                        @endif
                    </div>
                </div>
                @endforeach
@else
                <div class="col-span-full bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-wallet text-3xl text-gray-200"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-400 font-cairo">لا توجد صناديق مضافة</h3>
                    <p class="text-gray-300 font-almarai mt-2">ابدأ بإضافة أول صندوق مالي لمركزك</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Modal -->
    @if(auth()->user()->hasRole('super-admin'))
    <div id="createModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl transform transition-all">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                    <i class="fas fa-plus-circle text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold font-cairo text-gray-800">صندوق جديد</h2>
            </div>

            <form action="{{ route('funds.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المركز التابع له الصندوق</label>
                    <select name="center_id" required
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all">
                        <option value="">-- حدد المركز --</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}">{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">مسمى الصندوق</label>
                    <input type="text" name="name" required placeholder="مثال: الصندوق الرئيسي"
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">وصف الصندوق</label>
                    <textarea name="description" placeholder="حدد الغرض من الصندوق..."
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all"
                        rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الرصيد الحالي</label>
                    <div class="relative">
                        <input type="number" name="balance" step="0.01" required placeholder="0.00"
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-center font-bold text-xl text-primary   transition-all">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 font-almarai text-xs">ر.ي</span>
                    </div>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="submit" class="flex-[2] btn-primary py-4 rounded-2xl font-bold text-lg shadow-lg">حفظ
                        البيانات</button>
                    <button type="button" onclick="closeCreateModal()"
                        class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-[2.5rem] p-10 max-w-md w-full shadow-2xl transform transition-all">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary">
                    <i class="fas fa-edit text-xl"></i>
                </div>
                <h2 class="text-2xl font-bold font-cairo text-gray-800">تعديل الصندوق</h2>
            </div>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المركز التابع له الصندوق</label>
                    <select name="center_id" id="edit_center_id" required
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all">
                        <option value="">-- حدد المركز --</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}">{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">مسمى الصندوق</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">وصف الصندوق</label>
                    <textarea name="description" id="edit_description"
                        class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all"
                        rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الرصيد (للمراجعة
                        فقط)</label>
                    <div class="relative">
                        <input type="number" name="balance" id="edit_balance" step="0.01" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-center font-bold text-xl text-primary   transition-all">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 font-almarai text-xs">ر.ي</span>
                    </div>
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="submit"
                        class="flex-[2] bg-secondary text-white py-4 rounded-2xl font-bold text-lg shadow-lg hover:bg-orange-600 transition-all">تحديث
                        الصندوق</button>
                    <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        function openCreateModal() {
            showModal('createModal');
        }
        function closeCreateModal() {
            hideModal('createModal');
        }

        function openEditModal(fund) {
            document.getElementById('edit_center_id').value = fund.center_id;
            document.getElementById('edit_name').value = fund.name;
            document.getElementById('edit_description').value = fund.description || '';
            document.getElementById('edit_balance').value = fund.balance;
            
            // Generate the URL using Laravel's route helper to ensure correct pathing (especially in subfolders)
            let actionUrl = "{{ route('funds.update', ':id') }}";
            document.getElementById('editForm').action = actionUrl.replace(':id', fund.id);
            
            showModal('editModal');
        }
        function closeEditModal() {
            hideModal('editModal');
        }

        function showModal(id) {
            const m = document.getElementById(id);
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function hideModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    </script>
@endsection
