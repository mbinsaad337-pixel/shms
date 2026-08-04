@extends('layouts.nutrition')
@section('title', 'سجل التوزيع')

@section('content')
    <div class="p-6 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 font-cairo">سجل التوزيع اليومي</h2>
                <p class="text-gray-400 text-sm font-almarai">تتبع جميع عمليات توزيع الوجبات</p>
            </div>
            <a href="{{ route('nutrition.distributions.scan') }}"
                class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm shadow-lg shadow-teal-200">
                <i class="fas fa-qrcode"></i> بدء التوزيع
            </a>
        </div>

        <!-- Today Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-teal-50 border border-teal-100 rounded-2xl p-4 text-center">
                <p class="text-3xl font-black text-teal-700 font-cairo">{{ $todayStats['total_meals'] }}</p>
                <p class="text-xs text-teal-500 font-cairo mt-1">إجمالي اليوم</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 text-center">
                <p class="text-3xl font-black text-blue-700 font-cairo">{{ $todayStats['individual'] }}</p>
                <p class="text-xs text-blue-500 font-cairo mt-1">توزيع فردي</p>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded-2xl p-4 text-center">
                <p class="text-3xl font-black text-purple-700 font-cairo">{{ $todayStats['group_count'] }}</p>
                <p class="text-xs text-purple-500 font-cairo mt-1">إجمالي الحلق</p>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 text-center">
                <p class="text-3xl font-black text-orange-600 font-cairo">{{ $todayStats['extra_groups'] }}</p>
                <p class="text-xs text-orange-400 font-cairo mt-1">وجبات لاحقة (مجموعات)</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="flex flex-wrap gap-3 mb-5">
            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                class="border border-gray-200 rounded-xl px-4 py-2.5   text-sm focus:ring-2 focus:ring-teal-400">
            <select name="meal_type" class="border border-gray-200 rounded-xl px-4 py-2.5 font-cairo text-sm">
                <option value="">كل الوجبات</option>
                <option value="breakfast" {{ request('meal_type') === 'breakfast' ? 'selected' : '' }}>فطور</option>
                <option value="lunch" {{ request('meal_type') === 'lunch' ? 'selected' : '' }}>غداء</option>
                <option value="dinner" {{ request('meal_type') === 'dinner' ? 'selected' : '' }}>عشاء</option>
            </select>
            <select name="type" class="border border-gray-200 rounded-xl px-4 py-2.5 font-cairo text-sm">
                <option value="">كل الأنواع</option>
                <option value="individual" {{ request('type') === 'individual' ? 'selected' : '' }}>فردي</option>
                <option value="group" {{ request('type') === 'group' ? 'selected' : '' }}>مجمع</option>
                <option value="extra" {{ request('type') === 'extra' ? 'selected' : '' }}>لاحقة</option>
            </select>
            <button type="submit" class="bg-teal-600 text-white px-5 py-2.5 rounded-xl font-cairo font-bold text-sm">
                <i class="fas fa-filter ml-1"></i> تصفية
            </button>
        </form>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">المستلم / المجموعة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الوجبة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">نوع التوزيع</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">عدد الطلاب</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">التاريخ والوقت</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الموزع</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">التفاصيل</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if ($distributions->count() > 0)
                        @foreach ($distributions as $d)
                        @php
                            $isGroup = (bool) $d->group_name;
                            $displayName = $d->group_name ?? $d->student?->name_ar ?? 'طالب غير معروف';
                        @endphp
                        <tr
                            class="hover:bg-gray-50/50 transition-colors {{ $d->distribution_type === 'extra' ? 'bg-orange-50/30' : '' }}">
                            <td class="px-5 py-4 font-bold text-gray-800 font-cairo text-sm">{{ $displayName }}</td>
                            <td class="px-5 py-4 text-center">
                                @php $mealColors = ['breakfast' => 'bg-yellow-100 text-yellow-700', 'lunch' => 'bg-green-100 text-green-700', 'dinner' => 'bg-blue-100 text-blue-700']; @endphp
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-bold {{ $mealColors[$d->meal_type] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $d->getMealTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php $typeColors = ['individual' => 'bg-blue-50 text-blue-700', 'group' => 'bg-purple-50 text-purple-700', 'extra' => 'bg-orange-50 text-orange-700']; @endphp
                                <span
                                    class="px-2 py-1 rounded-lg text-xs font-bold {{ $typeColors[$d->distribution_type] ?? 'bg-gray-50 text-gray-500' }}">
                                    {{ $d->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center   text-gray-600 text-sm">
                                {{ $isGroup ? ($d->group_members_count ?? '—') : '1' }}
                            </td>
                            <td class="px-5 py-4 text-center   text-gray-500 text-sm">
                                {{ $d->distributed_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-5 py-4 text-gray-600 font-cairo text-sm">{{ $d->distributor->name }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($isGroup)
                                    <button onclick="showGroupDetails('{{ $d->id }}')"
                                        class="text-teal-600 hover:text-teal-800 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form action="{{ route('nutrition.distributions.destroy', $d->id) }}" method="POST"
                                    data-confirm="هل أنت متأكد من حذف هذا السجل؟ سيؤدي هذا للسماح بإعادة التوزيع للطلاب المرتبطين.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-300">
                                <i class="fas fa-qrcode text-5xl mb-3 block"></i>
                                <p class="font-cairo">لا توجد عمليات توزيع لهذا اليوم</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $distributions->withQueryString()->links() }}</div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div
            class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800 font-cairo">تفاصيل المجموعة</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="fas fa-times text-xl"></i></button>
            </div>
            <div id="modalContent" class="p-6 max-h-[60vh] overflow-y-auto">
                <!-- Data will be loaded here -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showGroupDetails(distId) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');
            modal.classList.remove('hidden');
            content.innerHTML = '<div class="text-center py-10"><i class="fas fa-spinner fa-spin text-3xl text-teal-500"></i><p class="mt-2 font-cairo text-gray-500">جارٍ التحميل...</p></div>';

            const url = "{{ route('nutrition.distributions.details', ':id') }}".replace(':id', distId);

            fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.students && data.students.length > 0) {
                        let html = '<div class="space-y-3">';
                        data.students.forEach(st => {
                            html += `
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-xs">
                                        ${st.name_ar ? st.name_ar.charAt(0) : '?'}
                                    </div>
                                    <span class="font-bold text-gray-700 font-cairo text-sm">${st.name_ar || 'غير معروف'}</span>
                                </div>
                            `;
                        });
                        html += '</div>';
                        content.innerHTML = html;
                    } else {
                        content.innerHTML = '<p class="text-center py-10 text-gray-400 font-cairo">لا توجد بيانات متاحة لهؤلاء الطلاب</p>';
                    }
                })
                .catch(err => {
                    content.innerHTML = '<p class="text-center py-10 text-red-500 font-cairo">حدث خطأ أثناء تحميل البيانات</p>';
                });
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
    </script>
@endpush
