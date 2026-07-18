@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
    <div class="max-w-4xl mx-auto mt-4 md:mt-8 px-4 sm:px-6 lg:px-8 mb-8 relative">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden font-cairo">
            <div class="px-5 py-4 md:px-8 md:py-6 border-b border-gray-50 flex justify-between items-center bg-navy/5">
                <h2 class="text-xl font-bold text-navy">البيانات الشخصية</h2>
                <i class="fas fa-user-edit text-gray-400 text-xl"></i>
            </div>

            <div class="p-5 md:p-8">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col md:flex-row gap-8 md:gap-10">
                        <!-- Right Side: Avatar -->
                        <div class="flex flex-col items-center gap-4 w-full md:w-1/3">
                            <div class="relative group cursor-pointer"
                                onclick="document.getElementById('avatar-input').click()">
                                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=004274&color=D4A044&bold=true' }}"
                                    id="avatar-preview"
                                    class="w-40 h-40 object-cover rounded-full border-4 border-gray-50 shadow-md group-hover:border-gold transition-colors">
                                <div
                                    class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-camera text-white text-2xl"></i>
                                </div>
                            </div>
                            <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*"
                                onchange="previewImage(this)">
                            <p class="text-xs text-gray-400 font-almarai text-center">انقر على الصورة لتغييرها<br>(الحد
                                الأقصى للملف 2 ميجابايت)</p>
                            @error('avatar') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Left Side: Inputs -->
                        <div class="flex-1 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right">الاسم الكامل
                                    <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-user absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full pl-4 pr-10 py-3 border-gray-200 rounded-xl focus:border-gold focus:ring-gold shadow-sm font-almarai transition-all">
                                </div>
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 font-cairo text-right">البريد
                                    الإلكتروني <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full pl-4 pr-10 py-3 border-gray-200 rounded-xl focus:border-gold focus:ring-gold shadow-sm font-almarai transition-all"
                                        dir="ltr">
                                </div>
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pt-4 mt-6 border-t border-gray-50 flex flex-col sm:flex-row gap-3 sm:gap-4">
                                <button type="submit"
                                    class="flex-1 bg-navy text-white text-center py-3 rounded-xl hover:bg-[#083358] shadow-lg font-bold transition-colors">
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('dashboard') }}"
                                    class="px-8 bg-gray-100 text-gray-600 text-center py-3 rounded-xl hover:bg-gray-200 font-bold transition-colors">
                                    إلغاء
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
