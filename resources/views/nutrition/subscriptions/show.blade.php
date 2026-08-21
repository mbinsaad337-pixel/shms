@extends ('layouts.nutrition')
@section ('title', 'تفاصيل الاشتراك')

@section ('content')
    @php $preview = $preview ?? false; $previewArchive = $previewArchive ?? null; @endphp
    <div class="p-6 max-w-4xl mx-auto">

        @include ('partials.print_header', (array)['title' => 'اشتراك تغذية: ' . ($subscription->student?->name_ar ?? 'طالب غير موجود'), 'number' => 'SUB-' . $subscription->id])

        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
                <a href="{{ route('nutrition.subscriptions.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 font-cairo">{{ $subscription->student?->name_ar ?? 'طالب غير موجود' }}</h2>
                    <p class="text-gray-400   text-sm">{{ $subscription->student?->university_id }}</p>
                </div>
            </div>
            <div class="flex gap-2 no-print">
                @if(!$preview)
                    <a href="{{ route('nutrition.subscriptions.export-pdf') }}?status={{ $subscription->status }}"
                        class="w-9 h-9 bg-gray-800 text-white rounded-xl flex items-center justify-center" title="تصدير PDF">
                        <i class="fas fa-file-pdf text-sm"></i>
                    </a>
                @elseif($preview && $previewArchive)
                    <a href="{{ route('annual-rollover.export-archive-pdf', $previewArchive) }}" target="_blank"
                        class="w-9 h-9 bg-gray-800 text-white rounded-xl flex items-center justify-center" title="تصدير PDF">
                        <i class="fas fa-file-pdf text-sm"></i>
                    </a>
                    <a href="{{ route('annual-rollover.index') }}"
                        class="w-9 h-9 bg-gray-100 text-gray-700 rounded-xl flex items-center justify-center">
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                @endif
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            @php $balance = $subscription->total_paid - $subscription->total_due; @endphp
            <div class="bg-red-50 border border-red-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-red-400 uppercase font-cairo mb-1">مدين (يستحق)</p>
                <p class="text-3xl font-black text-red-700  ">{{ number_format($subscription->total_due, 2) }}</p>
                <p class="text-xs text-red-400 font-cairo">{{ currency_symbol() }}</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-5 text-center">
                <p class="text-[10px] font-bold text-green-500 uppercase font-cairo mb-1">دائن (مدفوع)</p>
                <p class="text-3xl font-black text-green-700  ">{{ number_format($subscription->total_paid, 2) }}
                </p>
                <p class="text-xs text-green-400 font-cairo">{{ currency_symbol() }}</p>
            </div>
            <div
                class="{{ $balance >= 0 ? 'bg-blue-50 border-blue-100' : 'bg-orange-50 border-orange-100' }} border rounded-2xl p-5 text-center">
                <p
                    class="text-[10px] font-bold {{ $balance >= 0 ? 'text-blue-400' : 'text-orange-400' }} uppercase font-cairo mb-1">
                    {{ $balance >= 0 ? 'رصيد دائن' : 'رصيد مدين' }}
                </p>
                <p class="text-3xl font-black {{ $balance >= 0 ? 'text-blue-700' : 'text-orange-700' }}  ">
                    {{ number_format(abs($balance), 2) }}
                </p>
                <p class="text-xs {{ $balance >= 0 ? 'text-blue-400' : 'text-orange-400' }} font-cairo">{{ currency_symbol() }}</p>
            </div>
        </div>

        <!-- Subscription Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-800 font-cairo mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-purple-500"></i> تفاصيل الاشتراك
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">نوع الاشتراك</p>
                    <p class="font-bold text-gray-800 font-cairo">{{ $subscription->getTypeLabel() }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">الحالة</p>
                    <span class="px-2 py-0.5 rounded text-xs font-bold
                            {{ $subscription->status === 'active' ? 'bg-green-100 text-green-700' :
        ($subscription->status === 'suspended' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500') }}">
                        {{ $subscription->getStatusLabel() }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">عدد الأيام</p>
                    <p class="font-bold text-gray-800  ">{{ $subscription->days_count }} يوم</p>
                </div>
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">من</p>
                    <p class="  text-gray-700">{{ $subscription->start_date->format('Y-m-d') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">إلى</p>
                    <p class="  text-gray-700">{{ $subscription->end_date->format('Y-m-d') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-cairo text-xs mb-1">اشتراك يومي</p>
                    <p class="font-bold   text-gray-800">{{ number_format($subscription->daily_rate, 2) }} {{ currency_symbol() }}</p>
                </div>
                @if ($subscription->last_payment_date)
                    <div>
                        <p class="text-gray-400 font-cairo text-xs mb-1">آخر يوم للدفع</p>
                        <p class="  text-orange-600 font-bold">{{ $subscription->last_payment_date->format('Y-m-d') }}
                        </p>
                    </div>
                @endif
                @if ($subscription->qr_code)
                    <div class="md:col-span-2">
                        <p class="text-gray-400 font-cairo text-xs mb-1">رمز QR</p>
                        <p class="  text-gray-400 text-xs break-all">{{ $subscription->qr_code }}</p>
                    </div>
                @endif
            </div>
            @if ($subscription->status === 'suspended')
                <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-3">
                    <p class="text-red-600 font-cairo text-sm"><i class="fas fa-ban ml-1"></i><strong>سبب الإيقاف:</strong>
                        {{ $subscription->suspended_reason }}</p>
                </div>
            @endif
        </div>

        <!-- Distribution history -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 font-cairo">سجل الوجبات الموزعة</h3>
                <span class="text-xs text-gray-400">{{ $subscription->distributions->count() }} وجبة</span>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-500 font-cairo">التاريخ</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">الوجبة</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">النوع</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 font-cairo">رقم الصحن</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @if ($subscription->distributions->count() > 0))
    @foreach ($subscription->distributions->sortByDesc('distributed_at')->take(20) as $d)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3   text-gray-600 text-sm">{{ $d->distributed_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-5 py-3 text-center font-cairo text-sm">{{ $d->getMealTypeLabel() }}</td>
                            <td class="px-5 py-3 text-center font-cairo text-sm text-gray-500">{{ $d->getTypeLabel() }}</td>
                            <td class="px-5 py-3 text-center   text-gray-500 text-sm">{{ $d->dish_number ?? '—' }}</td>
                        </tr>
                        @endforeach
@else
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-300 font-cairo text-sm">لا توجد وجبات موزعة بعد
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @include('partials.print_footer')
    </div>
@endsection
