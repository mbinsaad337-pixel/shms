@extends('layouts.app')

@section('title', 'انتهت صلاحية الرمز')

@section('content')
    <div class="max-w-md mx-auto py-16 px-4 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 text-red-600 rounded-full mb-6">
            <i class="fas fa-clock fa-4x"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-navy font-cairo">عذراً، الرمز منتهي الصلاحية</h1>
        <p class="text-gray-600 font-almarai mt-4 text-lg">هذا الرمز المجمع لم يعد صالحاً للاستخدام، حيث انتهت صلاحيته في:
        </p>
        <div class="mt-4 inline-block px-6 py-2 bg-red-50 text-red-700 rounded-full font-bold font-almarai text-xl">
            {{ $group->expires_at->format('Y-m-d H:i') }}
        </div>

        <div class="mt-12">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-8 py-3 bg-navy text-white rounded-xl font-bold hover:shadow-lg transition-all font-cairo">
                العودة للرئيسية
            </a>
        </div>
    </div>
@endsection
