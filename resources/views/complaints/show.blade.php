@extends('layouts.app')
@section('title', $complaint->subject)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('complaints.inbox') }}"
           class="w-10 h-10 bg-white rounded-full shadow-sm flex items-center justify-center text-gray-400 hover:text-navy transition-all">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-xl font-black text-navy font-cairo leading-tight">{{ $complaint->subject }}</h1>
            <div class="flex items-center gap-2 mt-0.5">
                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold {{ $complaint->status_color }}">
                    {{ $complaint->status_label }}
                </span>
                @if($complaint->priority === 'urgent')
                    <span class="text-xs px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 font-bold">
                        <i class="fas fa-exclamation-circle ml-1"></i>عاجل
                    </span>
                @endif
            </div>
        </div>
        {{-- Delete --}}
        @if($complaint->sender_id === auth()->id())
            <form action="{{ route('complaints.destroy', $complaint) }}" method="POST"
                  data-confirm="هل أنت متأكد من حذف هذه الرسالة؟">
                @csrf
                <button type="submit"
                        class="w-10 h-10 bg-red-50 text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                    <i class="fas fa-trash-alt text-sm"></i>
                </button>
            </form>
        @endif
    </div>

    {{-- Thread --}}
    <div class="space-y-4">
        @foreach($thread as $message)
            @php
                $isMe = $message->sender_id === auth()->id();
            @endphp
            <div class="bg-white rounded-3xl shadow-sm border {{ $isMe ? 'border-gold/30 bg-gold/5' : 'border-gray-100' }} p-6">
                {{-- Message Header --}}
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                    <img src="{{ $message->sender->avatar ? asset('storage/' . $message->sender->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($message->sender->name) . '&background=004274&color=D4A044&bold=true' }}"
                         class="w-10 h-10 rounded-2xl border-2 border-gray-100 object-cover">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-navy font-cairo text-sm">{{ $message->sender->name }}</span>
                            @if($isMe)
                                <span class="text-[10px] text-gold bg-gold/10 px-2 py-0.5 rounded-full font-bold">أنت</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                            <span class="font-almarai">إلى: {{ $message->receiver->name }}</span>
                            @if($message->receiver->center)
                                <span>— {{ $message->receiver->center->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] text-gray-400  ">{{ $message->created_at->format('Y/m/d H:i') }}</div>
                        <div class="text-[10px] text-gray-300  ">{{ $message->created_at->diffForHumans() }}</div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="text-sm text-gray-700 font-almarai leading-loose whitespace-pre-line">
                    {{ $message->body }}
                </div>

                {{-- Attachment --}}
                @if($message->attachment)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-bold text-gray-500 font-cairo mb-2">
                            <i class="fas fa-paperclip ml-1"></i>المرفق:
                        </p>
                        @if($message->attachment_type === 'pdf')
                            <a href="{{ $message->attachment_url }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-sm font-bold font-cairo hover:bg-red-100 transition-all">
                                <i class="fas fa-file-pdf text-lg"></i>
                                فتح ملف PDF
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        @else
                            <a href="{{ $message->attachment_url }}" target="_blank" class="block">
                                <img src="{{ $message->attachment_url }}"
                                     class="max-h-64 rounded-2xl border border-gray-100 object-contain hover:scale-[1.02] transition-transform cursor-zoom-in shadow-sm">
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Reply Form --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-black text-navy font-cairo mb-4 flex items-center gap-2">
            <i class="fas fa-reply text-gold"></i>
            إرسال رد
        </h3>
        <form action="{{ route('complaints.reply', $complaint) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <textarea name="body" rows="4" required
                      placeholder="اكتب ردك هنا..."
                      class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 font-almarai text-sm focus:ring-4 focus:ring-navy/10 focus:border-navy outline-none transition-all resize-none"></textarea>

            <div x-data="{ fileName: '' }" class="relative">
                <label for="reply_attachment"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 cursor-pointer hover:border-navy transition-all group w-fit">
                    <i class="fas fa-paperclip text-gray-400 group-hover:text-navy transition-colors"></i>
                    <span class="text-sm font-almarai text-gray-400 group-hover:text-navy transition-colors"
                          x-text="fileName || 'إرفاق صورة أو PDF'"></span>
                </label>
                <input type="file" name="attachment" id="reply_attachment" accept=".jpg,.jpeg,.png,.gif,.pdf"
                       class="absolute inset-0 opacity-0 cursor-pointer w-[200px]"
                       @change="fileName = $event.target.files[0]?.name || ''">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-navy text-white px-7 py-3 rounded-2xl font-bold font-cairo hover:bg-gold hover:text-navy transition-all shadow-lg shadow-navy/20">
                    <i class="fas fa-paper-plane"></i>
                    إرسال الرد
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
