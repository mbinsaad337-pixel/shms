@extends('layouts.app')
@section('title', 'الشكاوى والإشعارات الواردة')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-navy/10 rounded-2xl flex items-center justify-center">
                <i class="fas fa-inbox text-navy text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black text-navy font-cairo">صندوق الوارد</h1>
                <p class="text-sm text-gray-400 font-almarai">
                    @if($unreadCount > 0)
                        <span class="text-red-500 font-bold">{{ $unreadCount }}</span> رسالة غير مقروءة
                    @else
                        جميع الرسائل مقروءة
                    @endif
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('complaints.sent') }}"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl font-bold font-cairo text-sm hover:border-navy hover:text-navy transition-all shadow-sm">
                <i class="fas fa-paper-plane"></i> المرسلة
            </a>
            <a href="{{ route('complaints.create') }}"
               class="inline-flex items-center gap-2 bg-navy text-white px-5 py-2.5 rounded-xl font-bold font-cairo text-sm hover:bg-gold hover:text-navy transition-all shadow-lg shadow-navy/20">
                <i class="fas fa-plus"></i>  إشعار جديد
            </a>
        </div>
    </div>

    {{-- Messages List --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
        @forelse($complaints as $complaint)
            <a href="{{ route('complaints.show', $complaint) }}"
               class="flex items-start gap-4 p-5 hover:bg-blue-50/30 transition-all group {{ $complaint->status === 'unread' ? 'bg-blue-50/50' : '' }}">

                {{-- Avatar --}}
                <div class="shrink-0">
                    <img src="{{ $complaint->sender->avatar ? asset('storage/' . $complaint->sender->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($complaint->sender->name) . '&background=004274&color=D4A044&bold=true' }}"
                         class="w-11 h-11 rounded-2xl border-2 {{ $complaint->status === 'unread' ? 'border-blue-200' : 'border-gray-100' }} object-cover">
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-bold text-navy font-cairo text-sm {{ $complaint->status === 'unread' ? '' : 'font-medium text-gray-700' }}">
                            {{ $complaint->sender->name }}
                        </span>
                        @if($complaint->sender->center)
                            <span class="text-xs text-gray-400 font-almarai">- {{ $complaint->sender->center->name }}</span>
                        @endif
                        @if($complaint->priority === 'urgent')
                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-[10px] font-bold font-cairo">
                                <i class="fas fa-exclamation-circle ml-1"></i>عاجل
                            </span>
                        @endif
                        @if($complaint->status === 'unread')
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        @endif
                    </div>
                    <p class="text-sm font-bold text-gray-800 font-cairo truncate">{{ $complaint->subject }}</p>
                    <p class="text-xs text-gray-400 font-almarai truncate mt-0.5">{{ Str::limit($complaint->body, 100) }}</p>
                </div>

                {{-- Meta --}}
                <div class="shrink-0 text-left">
                    <div class="text-[11px] text-gray-400   mb-1">{{ $complaint->created_at->diffForHumans() }}</div>
                    @if($complaint->attachment)
                        <div class="flex items-center gap-1 text-[10px] text-gray-400">
                            <i class="fas {{ $complaint->attachment_type === 'pdf' ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400' }}"></i>
                            مرفق
                        </div>
                    @endif
                    @if($complaint->replies->count() > 0)
                        <div class="flex items-center gap-1 text-[10px] text-emerald-500 mt-1">
                            <i class="fas fa-reply"></i>
                            {{ $complaint->replies->count() }} رد
                        </div>
                    @endif
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $complaint->status_color }}">
                        {{ $complaint->status_label }}
                    </span>
                </div>
            </a>
        @empty
            <div class="text-center py-20">
                <i class="fas fa-inbox text-5xl text-gray-100 mb-4 block"></i>
                <p class="text-gray-300 font-almarai text-sm">لا توجد رسائل واردة بعد</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($complaints->hasPages())
        <div class="flex justify-center">
            {{ $complaints->links() }}
        </div>
    @endif
</div>
@endsection
