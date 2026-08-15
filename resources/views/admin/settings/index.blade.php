@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-navy font-cairo">إعدادات النظام</h1>
            <p class="text-gray-500 mt-2 font-almarai">إدارة الإعدادات العامة للإشعارات والمالية وغيرها.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($settings as $group => $groupSettings)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h2 class="text-xl font-bold text-navy font-cairo">
                        @if($group == 'notifications')
                            <i class="fas fa-bell text-gold ml-2"></i> إعدادات الإشعارات والتنبيهات
                        @elseif($group == 'finance')
                            <i class="fas fa-money-bill-wave text-gold ml-2"></i> الإعدادات المالية
                        @else
                            <i class="fas fa-cog text-gold ml-2"></i> إعدادات {{ $group }}
                        @endif
                    </h2>
                </div>
                
                <div class="p-6 space-y-6">
                    @foreach($groupSettings as $setting)
                        <div>
                            <label class="block text-sm font-bold text-gray-700 font-cairo mb-2">{{ $setting->label }} ({{ $setting->key }})</label>
                            
                            @if($setting->key == 'overspend_approval_required')
                                <select name="settings[{{ $setting->key }}]" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-navy focus:border-navy block p-3 font-almarai transition-all">
                                    <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>نعم، تفعيل طلب الموافقة</option>
                                    <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>لا، السماح بالصرف مباشرة</option>
                                </select>
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ old('settings.'.$setting->key, $setting->value) }}" 
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-navy focus:border-navy block p-3 font-almarai transition-all">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" class="bg-navy text-white px-8 py-3 rounded-xl font-bold font-cairo hover:bg-navy/90 transition-all shadow-md">
                <i class="fas fa-save ml-2"></i> حفظ الإعدادات
            </button>
        </div>
    </form>
</div>
@endsection
