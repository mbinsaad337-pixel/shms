@extends ('layouts.app')
@php
    /** @var \App\Models\Vehicle[] $vehicles */
@endphp

@section ('content')
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-primary font-cairo">مركبات الطلاب</h2>
            <a href="{{ route('vehicles.create') }}"
                class="bg-secondary hover:bg-orange-600 text-white px-6 py-2 rounded-xl shadow-md font-cairo font-bold transition-all transform hover:scale-105">
                + تسجيل مركبة جديدة
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-right">
                        <th class="px-6 py-4 font-cairo text-gray-600">الطالب</th>
                        <th class="px-6 py-4 font-cairo text-gray-600">النوع / الموديل</th>
                        <th class="px-6 py-4 font-cairo text-gray-600">رقم اللوحة</th>
                        <th class="px-6 py-4 font-cairo text-gray-600">وثيقة المركبة</th>
                        <th class="px-6 py-4 font-cairo text-gray-600">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vehicles as $vehicle)
                        <tr class="border-b hover:bg-blue-50/20">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 font-almarai">{{ $vehicle->student->name_ar }}</div>
                                <div class="text-xs text-gray-500 font-almarai">{{ $vehicle->student->university_id }}</div>
                            </td>
                            <td class="px-6 py-4 font-almarai">{{ $vehicle->type }} {{ $vehicle->model }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 bg-gray-100 border border-gray-300 rounded font-bold font-almarai">{{ $vehicle->plate_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($vehicle->document_photo)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($vehicle->document_photo) }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center gap-1 font-almarai text-sm bg-blue-50 px-3 py-1 rounded-full border border-blue-100 w-fit transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        عرض
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm font-almarai px-3 py-1 bg-gray-50 rounded-full border border-gray-100 text-center inline-block">لا توجد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 flex gap-3 items-center">
                                <a href="{{ route('vehicles.edit', $vehicle) }}"
                                    class="text-blue-600 hover:text-blue-900 font-cairo underline decoration-dotted">تعديل</a>
                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                    data-confirm="هل أنت متأكد من حذف بيانات هذه المركبة؟ لا يمكن التراجع عن هذا الإجراء.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
