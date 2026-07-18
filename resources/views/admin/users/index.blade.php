@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8 flex justify-between items-center bg-white p-6 rounded-2xl border-l-8 border-gold shadow-sm">
            <div>
                <h1 class="text-3xl font-black text-navy font-cairo">إدارة موظفي المركز</h1>
                <p class="text-gray-400 font-almarai text-sm mt-1">إدارة حسابات وصلاحيات الطاقم الإداري والمالي</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.export-list', request()->all()) }}" target="_blank"
                    class="px-6 py-3 bg-white text-navy border-2 border-navy/10 rounded-xl hover:bg-navy/5 font-cairo font-bold transition-all flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print"></i>
                    <span>طباعة</span>
                </a>
                @if(auth()->user()->hasRole('super-admin'))
                <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-2">
                    <select name="center_id" onchange="this.form.submit()" 
                        class="px-4 py-2 rounded-xl border border-gray-100 bg-gray-50 focus:bg-white focus:border-gold outline-none text-sm font-cairo">
                        <option value="">كل المراكز</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ request('center_id') == $center->id ? 'selected' : '' }}>
                                {{ $center->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @endif
                
                @if(!auth()->user()->hasRole('super-admin'))
                <a href="{{ route('admin.users.create') }}"
                    class="px-6 py-3 bg-navy text-white rounded-xl hover:bg-navy/90 shadow-lg font-cairo font-bold transition-all transform hover:-translate-y-1 flex items-center gap-2">
                    <i class="fas fa-plus-circle text-gold"></i>
                    <span>إضافة موظف جديد</span>
                </a>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 uppercase text-xs font-black tracking-widest font-cairo">
                        <th class="px-6 py-4">الموظف</th>
                        <th class="px-6 py-4">البريد الإلكتروني</th>
                        <th class="px-6 py-4">الدور الوظيفي</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        @if(!auth()->user()->hasRole('super-admin'))
                            <th class="px-6 py-4 text-center">الإجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors">
                            <td class="px-5 py-4 text-sm">
                                <p class="text-gray-900 font-almarai">{{ $user->name }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm italic text-gray-500 font-almarai">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1.5 rounded-xl text-[10px] font-black bg-navy/5 text-navy font-cairo uppercase">
                                    {{ \App\Helpers\PermissionTranslationHelper::translateRole($user->getRoleNames()->first()) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="px-3 py-1.5 rounded-xl text-[10px] font-black {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} font-cairo">
                                    {{ $user->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                             @if(!auth()->user()->hasRole('super-admin'))
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="w-8 h-8 rounded-lg bg-navy/5 flex items-center justify-center text-navy hover:bg-navy hover:text-white transition-all shadow-sm"
                                            title="تعديل">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>

                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg {{ $user->is_active ? 'bg-red-50 text-red-500 hover:bg-red-500 hover:text-white' : 'bg-green-50 text-green-500 hover:bg-green-500 hover:text-white' }} flex items-center justify-center transition-all shadow-sm"
                                                title="{{ $user->is_active ? 'تعطيل' : 'تفعيل' }}">
                                                <i
                                                    class="fas {{ $user->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            data-confirm="هل أنت متأكد من حذف هذا الموظف؟ لا يمكن التراجع عن هذا الإجراء.">
                                            @csrf
                                            <button type="submit"
                                                class="w-8 h-8 rounded-lg bg-gray-50 text-gray-300 hover:bg-red-600 hover:text-white flex items-center justify-center transition-all shadow-sm"
                                                title="حذف">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
