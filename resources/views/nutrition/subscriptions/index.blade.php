@extends ('layouts.nutrition')
@section ('title', 'المشتركون في التغذية')

@section ('content')
    <div class="p-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">المشتركون في التغذية</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">إدارة اشتراكات الطلاب وتحصيل الرسوم المالية</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('nutrition.subscriptions.export-pdf', request()->all()) }}" target="_blank"
                    class="px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy hover:text-white shadow-sm font-cairo font-bold transition-all flex items-center gap-2 group">
                    <i class="fas fa-file-pdf text-red-500 group-hover:text-white transition-colors"></i>
                    <span>تصدير PDF</span>
                </a>
                <a href="{{ route('nutrition.subscriptions.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2 group">
                    <i class="fas fa-user-plus text-gold group-hover:rotate-12 transition-transform"></i>
                    <span>اشتراك جديد</span>
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="card-premium p-6 text-center border-b-4 border-b-green-500">
                <p class="text-4xl font-black text-navy font-mono">{{ $stats['active'] }}</p>
                <p class="text-[10px] font-black text-gray-400 font-cairo mt-2 uppercase tracking-widest">مشترك فعال</p>
            </div>
            <div class="card-premium p-6 text-center border-b-4 border-b-red-500">
                <p class="text-4xl font-black text-red-600 font-mono">{{ $stats['suspended'] }}</p>
                <p class="text-[10px] font-black text-gray-400 font-cairo mt-2 uppercase tracking-widest">مشترك موقوف</p>
            </div>
            <div class="card-premium p-6 text-center border-b-4 border-b-gray-400">
                <p class="text-4xl font-black text-gray-400 font-mono">{{ $stats['expired'] }}</p>
                <p class="text-[10px] font-black text-gray-400 font-cairo mt-2 uppercase tracking-widest">اشتراك منتهي</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="flex gap-4 mb-8 bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div class="relative flex-1">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم أو الرقم الجامعي..."
                    class="w-full border border-gray-100 bg-gray-50 rounded-xl pr-12 pl-4 py-3 font-cairo text-sm focus:ring-2 focus:ring-navy focus:bg-white transition-all">
            </div>
            <select name="status"
                class="border border-gray-100 bg-gray-50 rounded-xl px-6 py-3 font-cairo text-sm focus:ring-2 focus:ring-navy focus:bg-white transition-all min-w-[150px]">
                <option value="">كل الحالات</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>فعال</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>موقوف</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
            </select>
            <button type="submit" class="bg-gold text-navy px-8 py-3 rounded-xl font-cairo font-black text-sm hover:bg-gold/90 shadow-md">
                تصفية النتائج
            </button>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الطالب</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">نوع الاشتراك</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الفترة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الأيام</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">مدين</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">دائن</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الرصيد</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الحالة</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if (count($subscriptions) > 0)
                        @foreach ($subscriptions as $sub)
                            @php $balance = $sub->total_paid - $sub->total_due; @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-800 font-cairo text-sm">{{ $sub->student?->name_ar ?? 'طالب غير موجود' }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $sub->student?->university_id }}</p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-xs font-bold font-cairo px-2 py-1 bg-purple-50 text-purple-700 rounded-lg">
                                        {{ $sub->getTypeLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center font-mono text-gray-600 text-xs">
                                    {{ $sub->start_date->format('Y-m-d') }}<br>→ {{ $sub->end_date->format('Y-m-d') }}
                                </td>
                                <td class="px-5 py-4 text-center font-mono text-gray-700">{{ $sub->days_count }}</td>
                                <td class="px-5 py-4 text-center font-bold text-red-600 font-mono">
                                    {{ number_format($sub->total_due, 2) }}</td>
                                <td class="px-5 py-4 text-center font-bold text-green-600 font-mono">
                                    {{ number_format($sub->total_paid, 2) }}</td>
                                <td
                                    class="px-5 py-4 text-center font-bold font-mono {{ $balance >= 0 ? 'text-green-700' : 'text-red-600' }}">
                                    {{ number_format($balance, 2) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'active' => 'bg-emerald-100 text-emerald-700',
                                            'suspended' => 'bg-orange-100 text-orange-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                            'expired' => 'bg-gray-100 text-gray-500',
                                            'cancelled' => 'bg-gray-100 text-gray-400'
                                        ];
                                        $class = $statusClasses[$sub->status] ?? 'bg-gray-100 text-gray-400';
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold {{ $class }}">
                                        {{ $sub->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('nutrition.subscriptions.show', $sub) }}"
                                            class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                            title="تفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if ($sub->status === 'pending')
                                            <form action="{{ route('nutrition.subscriptions.approve', $sub) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-8 h-8 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                    title="اعتماد">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button onclick="rejectSubscription({{ $sub->id }})"
                                                class="w-8 h-8 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if ($sub->status === 'active')
                                            <button onclick="suspend({{ $sub->id }})"
                                                class="w-8 h-8 bg-orange-50 hover:bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                title="إيقاف مؤقت">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                            <button onclick="addPayment({{ $sub->id }}, {{ $sub->total_due - $sub->total_paid }})"
                                                class="w-8 h-8 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                title="تسجيل دفعة">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                        @elseif ($sub->status === 'suspended')
                                            <form action="{{ route('nutrition.subscriptions.activate', $sub) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-8 h-8 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                    title="تفعيل">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                            <button onclick="addPayment({{ $sub->id }}, {{ $sub->total_due - $sub->total_paid }})"
                                                class="w-8 h-8 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                title="تسجيل دفعة">
                                                <i class="fas fa-plus-circle"></i>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('nutrition.subscriptions.edit', $sub) }}"
                                            class="w-8 h-8 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                            title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('nutrition.subscriptions.destroy', $sub) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟ هذا الإجراء لا يمكن التراجع عنه.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-8 h-8 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center text-xs transition-all"
                                                title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="py-16 text-center text-gray-300">
                                <i class="fas fa-users text-5xl mb-3 block"></i>
                                <p class="font-cairo">لا يوجد مشتركون حتى الآن</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $subscriptions->withQueryString()->links() }}</div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4"><i class="fas fa-ban text-red-500 ml-2"></i>إيقاف الاشتراك
            </h3>
            <form id="suspendForm" method="POST">
                @csrf
                <input type="text" name="suspended_reason" required placeholder="سبب الإيقاف..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-red-400 mb-4">
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('suspendModal').classList.add('hidden')"
                        class="px-6 py-2.5 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 text-white rounded-xl font-bold font-cairo">إيقاف</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4"><i class="fas fa-money-bill text-green-500 ml-2"></i>تسجيل دفعة</h3>
            <form id="paymentForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600 font-cairo mb-1.5">المبلغ (ر.ي)</label>
                    <input type="number" name="amount" step="0.01" min="0.01" id="paymentAmount"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 font-mono text-lg font-bold focus:ring-2 focus:ring-green-400">
                    <p id="remainingBalance" class="text-xs text-gray-400 font-cairo mt-1"></p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                        class="px-6 py-2.5 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold font-cairo">تسجيل الدفعة</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4"><i class="fas fa-times text-rose-500 ml-2"></i>رفض طلب الاشتراك
            </h3>
            <form id="rejectForm" method="POST">
                @csrf
                <label class="block text-sm font-bold text-gray-600 font-cairo mb-2">سبب الرفض <span class="text-rose-500">*</span></label>
                <input type="text" name="rejection_reason" required placeholder="مثال: يرجى مراجعة إدارة التغذية..."
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 font-almarai text-sm focus:ring-2 focus:ring-rose-400 mb-4">
                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                        class="px-6 py-2.5 border border-gray-200 rounded-xl font-cairo font-bold text-gray-600">إلغاء</button>
                    <button type="submit"
                        class="px-6 py-2.5 bg-rose-600 text-white rounded-xl font-bold font-cairo">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push ('scripts')
    <script>
        function suspend(id) {
            document.getElementById('suspendForm').action = `{{ url('nutrition/subscriptions') }}/${id}/suspend`;
            document.getElementById('suspendModal').classList.remove('hidden');
        }
        function rejectSubscription(id) {
            document.getElementById('rejectForm').action = `{{ url('nutrition/subscriptions') }}/${id}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function addPayment(id, remaining) {
            document.getElementById('paymentForm').action = `{{ url('nutrition/subscriptions') }}/${id}/payment`;
            document.getElementById('paymentAmount').value = remaining.toFixed(2);
            document.getElementById('remainingBalance').textContent = `المتبقي: ${remaining.toFixed(2)} ر.ي`;
            document.getElementById('paymentModal').classList.remove('hidden');
        }
    </script>
@endpush
