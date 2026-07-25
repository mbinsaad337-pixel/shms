@extends('layouts.app')
@section('title', 'إرسال شكوى أو إشعار')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Back --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('complaints.inbox') }}"
           class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
            <i class="fas fa-arrow-right"></i>
        </a>
        <h1 class="text-xl font-black text-navy font-cairo">شكوى / إشعار جديد</h1>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Recipient --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">
                    <i class="fas fa-user-circle ml-2 text-gold"></i>المستلم <span class="text-red-500">*</span>
                </label>
                <select name="receiver_id" required id="receiver_id"
                        class="w-full px-5 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy outline-none transition-all @error('receiver_id') border-red-400 @enderror">
                    <option value="">-- اختر المستلم --</option>
                    @foreach($recipients as $user)
                        <option value="{{ $user->id }}" {{ old('receiver_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                            @if($user->center) — {{ $user->center->name }} @endif
                            ({{ $user->getRoleNames()->first() === 'super-admin' ? 'المدير العام' : ($user->getRoleNames()->first() === 'center-manager' ? 'مدير مركز' : $user->getRoleNames()->first()) }})
                        </option>
                    @endforeach
                </select>
                @error('receiver_id')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">
                    <i class="fas fa-flag ml-2 text-gold"></i>الأولوية
                </label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="priority" value="normal" {{ old('priority', 'normal') === 'normal' ? 'checked' : '' }}
                               class="w-4 h-4 text-navy accent-navy">
                        <span class="font-almarai text-sm text-gray-600 group-hover:text-navy transition-colors">عادي</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" name="priority" value="urgent" {{ old('priority') === 'urgent' ? 'checked' : '' }}
                               class="w-4 h-4 text-red-500 accent-red-500">
                        <span class="font-almarai text-sm text-red-500 font-bold group-hover:text-red-600 transition-colors">
                            <i class="fas fa-exclamation-triangle ml-1"></i>عاجل
                        </span>
                    </label>
                </div>
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">
                    <i class="fas fa-tag ml-2 text-gold"></i>الموضوع <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" value="{{ old('subject') }}" required
                       placeholder="عنوان الشكوى أو الإشعار..."
                       class="w-full px-5 py-3.5 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy outline-none transition-all @error('subject') border-red-400 @enderror">
                @error('subject')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Body --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">
                    <i class="fas fa-align-right ml-2 text-gold"></i>نص الرسالة <span class="text-red-500">*</span>
                </label>
                <textarea name="body" rows="6" required
                          placeholder="اكتب تفاصيل الشكوى أو الإشعار هنا..."
                          class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy outline-none transition-all resize-none @error('body') border-red-400 @enderror">{{ old('body') }}</textarea>
                @error('body')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Attachment --}}
            <div>
                <label class="block text-sm font-bold text-navy font-cairo mb-2">
                    <i class="fas fa-paperclip ml-2 text-gold"></i>المرفق <span class="text-gray-400 font-normal">(اختياري - صورة أو PDF)</span>
                </label>
                <div x-data="{ fileName: '' }" class="relative">
                    <label for="attachment"
                           class="flex items-center gap-3 px-5 py-4 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 cursor-pointer hover:border-navy hover:bg-blue-50/30 transition-all group">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-cloud-upload-alt text-navy text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-navy font-cairo" x-text="fileName || 'اختر ملفاً للإرفاق'"></p>
                            <p class="text-xs text-gray-400 font-almarai">JPG, PNG, GIF, PDF — الحد الأقصى 10 ميجابايت</p>
                        </div>
                    </label>
                    <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf"
                           class="absolute inset-0 opacity-0 cursor-pointer"
                           @change="fileName = $event.target.files[0]?.name || ''">
                </div>
                @error('attachment')<p class="text-red-500 text-xs mt-1 font-almarai">{{ $message }}</p>@enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('complaints.inbox') }}"
                   class="text-gray-400 hover:text-navy font-cairo text-sm transition-colors">إلغاء</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-navy text-white px-8 py-3 rounded-2xl font-bold font-cairo hover:bg-gold hover:text-navy transition-all shadow-lg shadow-navy/20">
                    <i class="fas fa-paper-plane"></i>
                    إرسال
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
