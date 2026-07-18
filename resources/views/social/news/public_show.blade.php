<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $news->title }} - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=Almarai:wght@300;400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --navy: #004274;
            --gold: #D4A044;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            background: linear-gradient(135deg, var(--navy) 0%, #002a50 100%);
            padding: 5rem 0 10rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--gold) 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            opacity: 0.1;
        }

        .header .container {
            position: relative;
            z-index: 5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .article-card {
            background: white;
            border-radius: 2.5rem;
            margin-top: -6rem;
            box-shadow: 0 25px 60px -15px rgba(0, 66, 116, 0.15);
            overflow: hidden;
            margin-bottom: 4rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 10;
        }

        .cover-image {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .article-content {
            padding: 3rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .badge-sports {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-culture {
            background: #ede9fe;
            color: #7c3aed;
        }

        .badge-achievement {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-general {
            background: #e0edf7;
            color: #004274;
        }

        .meta {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #f1f5f9;
        }

        h1 {
            color: var(--navy);
            font-size: 2.8rem;
            font-weight: 900;
            line-height: 1.3;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .body-text {
            font-family: 'Almarai', sans-serif;
            font-size: 1.15rem;
            line-height: 2;
            color: #334155;
            white-space: pre-wrap;
        }

        .gallery {
            display: grid;
            grid-template-cols: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 3.5rem;
        }

        .gallery-item {
            border-radius: 1rem;
            overflow: hidden;
            height: 200px;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: scale(1.03);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            font-weight: 700;
            transition: opacity 0.2s;
        }

        .back-btn:hover {
            opacity: 0.8;
        }

        @media (max-width: 768px) {
            .header {
                padding: 3rem 0 7rem;
            }

            .article-card {
                margin-top: -4rem;
                border-radius: 1.5rem;
            }

            .article-content {
                padding: 1.5rem;
            }

            .cover-image {
                height: 250px;
            }

            h1 {
                font-size: 1.6rem;
                margin-bottom: 1rem;
            }

            .meta {
                gap: 1rem;
                padding-bottom: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <div class="container flex justify-between items-center">
            <a href="{{ url('/') }}" class="back-btn">
                <i class="fas fa-arrow-right"></i>
                <span>العودة للرئيسية</span>
            </a>
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" alt="Logo"
                    class="h-10 w-auto filter brightness-0 invert">
            </div>
        </div>
    </header>

    <div class="container">
        <article class="article-card">
            @if($news->cover_image)
                <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="cover-image">
            @else
                <div style="height:200px; background:var(--navy); display:flex; align-items:center; justify-content:center">
                    <i class="fas fa-newspaper text-white/10" style="font-size:5rem"></i>
                </div>
            @endif

            <div class="article-content">
                @php
                    $catIcons = [
                        'sports' => ['emoji' => '⚽', 'label' => 'رياضي', 'cls' => 'badge-sports'],
                        'culture' => ['emoji' => '📚', 'label' => 'ثقافي', 'cls' => 'badge-culture'],
                        'achievement' => ['emoji' => '🏆', 'label' => 'إنجاز', 'cls' => 'badge-achievement'],
                        'general' => ['emoji' => '📰', 'label' => 'عام', 'cls' => 'badge-general'],
                    ];
                    $info = $catIcons[$news->category] ?? $catIcons['general'];
                @endphp

                <div class="text-center">
                    <div class="badge {{ $info['cls'] }}">
                        <span>{{ $info['emoji'] }}</span>
                        <span>{{ $info['label'] }}</span>
                    </div>
                </div>

                <h1>{{ $news->title }}</h1>

                <div class="meta">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-university text-gold"></i>
                        <span>{{ $news->center->name ?? 'مركز غير محدد' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-day text-gold"></i>
                        <span>{{ $news->published_at ? $news->published_at->format('Y/m/d') : $news->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>

                <div class="body-text">
                    {{ $news->body }}
                </div>

                @if($news->gallery && is_array($news->gallery) && count($news->gallery) > 0)
                    <div class="gallery">
                        @foreach($news->gallery as $img)
                            <div class="gallery-item">
                                <a href="{{ asset('storage/' . $img) }}">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        {{-- Related News --}}
        @if(isset($related) && count($related) > 0)
            <div class="mb-20">
                <h2 class="text-2xl font-black text-navy mb-8">أخبار ذات صلة</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $rel)
                        <a href="{{ route('news.public-show', $rel) }}" class="block no-underline" style="color:inherit">
                            <div
                                class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-md transition-shadow h-full">
                                <div style="height:140px">
                                    @if($rel->cover_image)
                                        <img src="{{ asset('storage/' . $rel->cover_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-200 text-3xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h4 class="font-bold text-navy text-sm line-clamp-2">{{ $rel->title }}</h4>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <footer style="background:#f1f5f9; padding:3rem 0; text-align:center">
        <p style="color:#94a3b8; font-size:0.85rem">
            &copy; {{ date('Y') }} نظام إدارة السكن الطلابي — جمعية رعاية طالب العلم
        </p>
    </footer>

</body>

</html>
