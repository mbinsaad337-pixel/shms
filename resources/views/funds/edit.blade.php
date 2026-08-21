@extends('layouts.app')

@section('title', 'تعديل الصندوق: ' . $fund->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('funds.index') }}" class="px-6 py-3 bg-gray-50 text-navy rounded-2xl hover:bg-gray-100 font-cairo font-bold transition-all inline-flex items-center gap-2 border border-gray-100">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع للقائمة</span>
                </a>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-secondary px-10 py-8 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-edit text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold font-cairo">تعديل الصندوق</h2>
                            <p class="text-white/80 font-almarai mt-1">تحديث بيانات الصندوق المالي</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('funds.update', $fund) }}" method="POST" class="p-10 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">المركز التابع له الصندوق</label>
                        <select name="center_id" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all font-almarai">
                            <option value="">-- حدد المركز --</option>
                            @foreach($centers as $center)
                                <option value="{{ $center->id }}" {{ old('center_id', $fund->center_id) == $center->id ? 'selected' : '' }}>{{ $center->name }}</option>
                            @endforeach
                        </select>
                        @error('center_id') <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">مسمى الصندوق</label>
                        <input type="text" name="name" required value="{{ old('name', $fund->name) }}"
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all font-almarai">
                        @error('name') <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">وصف الصندوق</label>
                        <textarea name="description" rows="3"
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all font-almarai">{{ old('description', $fund->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">عملة الصندوق</label>
                        <select name="currency" id="currency_select" required
                            class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-right transition-all font-almarai">
                            <option value="YER" {{ old('currency', $fund->currency) == 'YER' ? 'selected' : '' }}>ريال يمني</option>
                            <option value="SAR" {{ old('currency', $fund->currency) == 'SAR' ? 'selected' : '' }}>ريال سعودي</option>
                            <option value="USD" {{ old('currency', $fund->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي</option>
                        </select>
                        @error('currency') <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3 font-cairo text-right">الرصيد (للمراجعة فقط)</label>
                        <div class="relative">
                            <input type="number" name="balance" step="0.01" required value="{{ old('balance', $fund->balance) }}"
                                class="w-full px-5 py-4 rounded-2xl border border-gray-100 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none text-center font-bold text-xl text-primary transition-all">
                            <span id="currency_suffix" class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-300 font-almarai text-xs">{{ currency_symbol(old('currency', $fund->currency)) }}</span>
                        </div>
                        @error('balance') <p class="text-red-500 text-xs mt-2 font-almarai">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-4 pt-6">
                        <button type="submit" class="flex-[2] bg-secondary text-white py-4 rounded-2xl font-bold text-lg shadow-lg font-cairo hover:bg-orange-600 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>تحديث الصندوق</span>
                        </button>
                        <a href="{{ route('funds.index') }}" class="flex-1 bg-gray-100 text-gray-500 py-4 rounded-2xl font-bold font-cairo hover:bg-gray-200 transition-colors flex items-center justify-center">
                            إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const currencySymbols = { YER: 'ر.ي', SAR: 'ر.س', USD: '$' };
        const select = document.getElementById('currency_select');
        const suffix = document.getElementById('currency_suffix');
        select.addEventListener('change', function () {
            suffix.textContent = currencySymbols[this.value] || '{{ currency_symbol() }}';
        });
        suffix.textContent = currencySymbols[select.value] || '{{ currency_symbol() }}';
    </script>
@endsection
