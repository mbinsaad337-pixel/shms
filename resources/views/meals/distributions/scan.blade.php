@extends('layouts.app')

@section('title', 'واجهة توزيع الوجبات')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-gray-900 rounded-3xl p-8 shadow-2xl border-4 border-gray-800">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">نظام توزيع الوجبات الذكي</h1>
                <p class="text-gray-400">امسح باركود الطالب لتسجيل استلام الوجبة</p>
            </div>

            <div class="flex flex-col items-center">
                <!-- Scan Input (Focused automatically) -->
                <input type="text" id="barcode-input" autofocus
                    class="w-full max-w-md bg-gray-800 border-2 border-primary text-white text-center text-4xl py-6 rounded-2xl focus:outline-none focus:ring-4 focus:ring-primary/50 transition-all mb-8 shadow-inner"
                    placeholder="0000000000">

                <!-- Result Display Area -->
                <div id="result-card" class="w-full hidden transform transition-all duration-300">
                    <div id="status-bg" class="rounded-2xl p-8 text-center shadow-lg border-b-8">
                        <div id="result-icon" class="text-6xl mb-4"></div>
                        <h2 id="student-name" class="text-4xl font-bold mb-4"></h2>
                        <p id="result-message" class="text-2xl font-medium"></p>
                        <div id="meal-info" class="mt-6 text-xl opacity-80 grid grid-cols-2 gap-4">
                            <div class="bg-white/10 p-4 rounded-xl">
                                <span class="block text-sm">الوجبة</span>
                                <span id="meal-type" class="font-bold"></span>
                            </div>
                            <div class="bg-white/10 p-4 rounded-xl">
                                <span class="block text-sm">الوقت</span>
                                <span id="scan-time" class="font-bold"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ready State Placeholder -->
                <div id="ready-placeholder" class="text-center py-20 opacity-30 select-none">
                    <div class="text-8xl mb-4 text-gray-500">📷</div>
                    <div class="text-2xl text-gray-400">جاهز للمسح...</div>
                </div>
            </div>
        </div>

        <!-- Live Feed / Recent Scans -->
        <div class="mt-8 bg-white rounded-2xl p-6 shadow-sm border">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full ml-2 animate-pulse"></span>
                آخر عمليات الاستلام (مباشر)
            </h3>
            <div id="recent-scans" class="space-y-3">
                <!-- Js populated -->
            </div>
        </div>
    </div>

    <script>
        const input = document.getElementById('barcode-input');
        const resultCard = document.getElementById('result-card');
        const statusBg = document.getElementById('status-bg');
        const studentName = document.getElementById('student-name');
        const resultMessage = document.getElementById('result-message');
        const mealType = document.getElementById('meal-type');
        const scanTime = document.getElementById('scan-time');
        const resultIcon = document.getElementById('result-icon');
        const placeholder = document.getElementById('ready-placeholder');

        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const barcode = this.value;
                this.value = '';
                processScan(barcode);
            }
        });

        // Keep focus
        document.addEventListener('click', () => input.focus());

        async function processScan(barcode) {
            try {
                const response = await fetch('{{ route("distributions.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ barcode })
                });

                const data = await response.json();
                showResult(data);
                if (data.success) addToHistory(data);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function showResult(data) {
            placeholder.classList.add('hidden');
            resultCard.classList.remove('hidden');

            if (data.success) {
                statusBg.className = 'rounded-2xl p-8 text-center shadow-lg border-b-8 bg-green-500 text-white border-green-700';
                resultIcon.innerText = '✅';
                studentName.innerText = data.student_name;
                resultMessage.innerText = 'مسموح - تم تسجيل الاستلام';
                mealType.innerText = data.meal_type;
                scanTime.innerText = data.time;
            } else {
                statusBg.className = 'rounded-2xl p-8 text-center shadow-lg border-b-8 bg-red-600 text-white border-red-800';
                resultIcon.innerText = '❌';
                studentName.innerText = 'تنبيه!';
                resultMessage.innerText = data.message;
                mealType.innerText = '---';
                scanTime.innerText = '---';
            }

            // Reset after 4 seconds
            setTimeout(() => {
                resultCard.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }, 4000);
        }

        function addToHistory(data) {
            const history = document.getElementById('recent-scans');
            const item = document.createElement('div');
            item.className = 'flex justify-between items-center p-3 bg-gray-50 rounded-xl border-r-4 border-green-500 animate-slide-in';
            item.innerHTML = `
                <span class="font-bold text-gray-700">${data.student_name}</span>
                <span class="text-xs text-gray-500">${data.meal_type} | ${data.time}</span>
            `;
            history.prepend(item);
            if (history.children.length > 5) history.lastChild.remove();
        }
    </script>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(20px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
@endsection
