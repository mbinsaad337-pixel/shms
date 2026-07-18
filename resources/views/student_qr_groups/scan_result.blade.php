@extends('layouts.app')

@section('title', 'نتائج مسح الرمز')

@section('content')
    <div class="max-w-2xl mx-auto py-12 px-4 shadow-2xl rounded-3xl bg-white mt-10">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 text-green-600 rounded-full mb-4">
                <i class="fas fa-check-circle fa-3x"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-navy font-cairo">بيانات الرمز المجمع</h1>
            <p class="text-gray-500 font-almarai mt-2">المسؤول: {{ $group->primaryStudent->name_ar }}</p>
        </div>

        <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-700 font-cairo mr-2 border-r-4 border-gold pr-3">قائمة الطلاب المعتمدين
            </h3>

            @foreach($group->json_data['students'] as $index => $data)
                <div class="p-6 rounded-2xl border border-gray-100 bg-gray-50 hover:border-navy/20 transition-all">
                    <div class="flex justify-between items-start">
                        <div class="flex space-x-4 space-x-reverse">
                            <span
                                class="w-10 h-10 bg-navy text-white rounded-xl flex items-center justify-center font-bold">{{ $index + 1 }}</span>
                            <div>
                                <h4 class="text-xl font-bold text-navy font-cairo">{{ $data['name'] }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 mt-2">
                                    <p class="text-sm text-gray-600 font-almarai"><span class="font-bold">الرقم الجامعي:</span>
                                        {{ $data['id'] }}</p>
                                    <p class="text-sm text-gray-600 font-almarai"><span class="font-bold">التخصص:</span>
                                        {{ $data['major'] }}</p>
                                    <p class="text-sm text-gray-600 font-almarai"><span class="font-bold">الكلية:</span>
                                        {{ $data['college'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 pt-8 border-t border-dashed border-gray-200 text-center">
            <p class="text-xs text-gray-400 font-almarai italic">تم إنشاء هذا الرمز في
                {{ $group->created_at->format('Y-m-d H:i') }}</p>
            <p class="text-xs text-gray-400 font-almarai italic">صالح حتى {{ $group->expires_at->format('Y-m-d H:i') }}</p>
        </div>
    </div>
@endsection
