@extends('layouts.app')

@section('title', 'الإجراءات الإدارية')

@push('styles')
<style>
    .admin-tab-btn {
        @apply flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-2xl transition-all whitespace-nowrap border border-transparent;
    }
    .admin-tab-btn.active {
        background: var(--primary-navy);
        color: white;
        box-shadow: 0 4px 14px rgba(0,66,116,0.20);
    }
    .admin-tab-btn:not(.active) {
        @apply text-gray-500 hover:bg-gray-100 bg-white border-gray-100;
    }
    .badge-pending {
        @apply bg-amber-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-full min-w-[18px] text-center leading-none;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6" x-data="adminTabs()" x-init="init()">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-3xl border-r-8 border-navy shadow-sm gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-black text-navy font-cairo">الإجراءات الإدارية</h1>
            <p class="text-gray-400 font-almarai text-sm mt-1">إدارة موحدة للتعهدات والغياب والاستئذان والمخالفات والعقوبات</p>
        </div>
        <div class="flex items-center gap-2">
            @if($pendingLeavesCount > 0)
            <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-xl font-cairo text-sm font-bold">
                <i class="fas fa-bell animate-pulse"></i>
                <span>{{ $pendingLeavesCount }} طلب استئذان بانتظار الموافقة</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Tabs Navigation ────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-2 mb-6 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
        <button @click="setTab('commitments')" :class="tab === 'commitments' ? 'active' : ''" class="admin-tab-btn">
            <i class="fas fa-file-signature text-sm"></i>
            <span>تسجيل التعهد</span>
        </button>
        <button @click="setTab('absences')" :class="tab === 'absences' ? 'active' : ''" class="admin-tab-btn">
            <i class="fas fa-user-times text-sm"></i>
            <span>تسجيل الغياب</span>
        </button>
        <button @click="setTab('leaves')" :class="tab === 'leaves' ? 'active' : ''" class="admin-tab-btn">
            <i class="fas fa-door-open text-sm"></i>
            <span>الاستئذان</span>
            @if($pendingLeavesCount > 0)
            <span class="badge-pending">{{ $pendingLeavesCount > 9 ? '9+' : $pendingLeavesCount }}</span>
            @endif
        </button>
        <button @click="setTab('violations')" :class="tab === 'violations' ? 'active' : ''" class="admin-tab-btn">
            <i class="fas fa-gavel text-sm text-red-400"></i>
            <span>سجل المخالفات</span>
        </button>
        <button @click="setTab('penalties')" :class="tab === 'penalties' ? 'active' : ''" class="admin-tab-btn">
            <i class="fas fa-calendar-minus text-sm text-orange-400"></i>
            <span>سجل العقوبات</span>
        </button>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Commitments (التعهدات)                                         --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'commitments'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @include('administrative.partials.commitments_tab')
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Absences (الغياب)                                              --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'absences'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @include('administrative.partials.absences_tab')
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Leaves / Istizhan (الاستئذان)                                 --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'leaves'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @include('administrative.partials.leaves_tab')
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Violations (المخالفات)                                         --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'violations'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @include('administrative.partials.violations_tab')
    </div>

    {{-- ════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: Penalties (العقوبات)                                           --}}
    {{-- ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'penalties'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @include('administrative.partials.penalties_tab')
    </div>

</div>
@endsection

@push('scripts')
<script>
function adminTabs() {
    return {
        tab: '{{ request("tab", "commitments") }}',
        init() {
            // Keep tab in sync with URL
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tab');
            if (urlTab) this.tab = urlTab;
        },
        setTab(t) {
            this.tab = t;
            const url = new URL(window.location.href);
            url.searchParams.set('tab', t);
            // Remove other tab's page params
            ['c_page','a_page','l_page','v_page','p_page'].forEach(k => url.searchParams.delete(k));
            history.replaceState(null, '', url.toString());
        }
    }
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.tom-select').forEach((el) => {
            new TomSelect(el, {
                plugins: ['remove_button'],
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results px-3 py-2 text-gray-500 font-cairo text-sm">لا توجد نتائج تطابق بحثك</div>';
                    }
                }
            });
        });
    });
</script>
@endpush
