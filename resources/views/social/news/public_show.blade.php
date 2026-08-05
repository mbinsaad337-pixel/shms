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
            --bg-light: #f8fafc;
        }

        body {
            background-color: var(--bg-light);
            color: #1e293b;
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }

        /* ── Header ── */
        .site-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--navy);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            background: #f1f5f9;
            transition: all 0.2s;
        }

        .back-btn:hover {
            background: #e2e8f0;
            color: #002a50;
        }

        /* ── Layout ── */
        .main-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2.5rem;
        }

        @media (max-width: 992px) {
            .main-container {
                grid-template-columns: 1fr;
            }
        }

        /* ── Article Article ── */
        .article-main {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .article-header {
            padding: 2rem 2rem 0;
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .badge-sports { background: #dbeafe; color: #1d4ed8; }
        .badge-culture { background: #ede9fe; color: #7c3aed; }
        .badge-achievement { background: #fef3c7; color: #b45309; }
        .badge-general { background: #e0edf7; color: var(--navy); }

        .article-title {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--navy);
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: #64748b;
            font-size: 0.9rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .article-cover {
            width: 100%;
            height: 450px;
            object-fit: cover;
            margin-top: 1.5rem;
        }

        .article-body {
            padding: 2rem;
            font-family: 'Almarai', sans-serif;
            font-size: 1.1rem;
            line-height: 2;
            color: #334155;
            white-space: pre-wrap;
        }

        /* ── Engagement Bar ── */
        .engagement-bar {
            padding: 1rem 2rem;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            background: #fafaf9;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: #64748b;
            transition: color 0.2s;
            padding: 0;
        }

        .action-btn:hover {
            color: var(--navy);
        }

        .action-btn.liked {
            color: #ef4444;
        }

        /* ── Comments Section ── */
        .comments-section {
            padding: 2rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comment-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .comment-content {
            flex: 1;
            background: #f8fafc;
            padding: 1rem 1.2rem;
            border-radius: 0 1rem 1rem 1rem;
        }

        .comment-author {
            font-weight: 800;
            font-size: 0.95rem;
            color: var(--navy);
            margin-bottom: 4px;
        }

        .comment-time {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-bottom: 8px;
            display: block;
        }

        .comment-text {
            font-size: 0.95rem;
            color: #475569;
            line-height: 1.6;
            font-family: 'Almarai', sans-serif;
        }

        .comment-form-container {
            margin-top: 2rem;
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
        }

        .comment-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 0.75rem;
            padding: 1rem;
            font-family: 'Almarai', sans-serif;
            font-size: 0.95rem;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 1rem;
        }

        .comment-input:focus {
            outline: none;
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(0, 66, 116, 0.1);
        }

        .btn-submit {
            background: var(--navy);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #002a50;
        }

        /* ── Sidebar ── */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .widget {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 15px -5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .widget-title {
            font-size: 1.2rem;
            font-weight: 900;
            color: var(--navy);
            margin-bottom: 1.2rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f1f5f9;
            position: relative;
        }

        .widget-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 50px;
            height: 2px;
            background: var(--gold);
        }

        .related-card {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px dotted #e2e8f0;
            text-decoration: none;
        }

        .related-card:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .related-img {
            width: 80px;
            height: 80px;
            border-radius: 0.75rem;
            object-fit: cover;
            flex-shrink: 0;
        }

        .related-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.5;
            transition: color 0.2s;
        }

        .related-card:hover .related-title {
            color: var(--gold);
        }
        
        .gallery-grid {
            display: grid;
            grid-template-cols: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            padding: 0 2rem 2rem;
        }
        .gallery-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .gallery-img:hover {
            transform: scale(1.02);
        }

        .login-prompt {
            background: #f1f5f9;
            padding: 1.5rem;
            text-align: center;
            border-radius: 1rem;
            color: #475569;
            font-weight: 600;
        }
        .login-prompt a {
            color: var(--navy);
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <header class="site-header">
        <div class="header-container">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/logos/alawayil_logo.png') }}" alt="Logo" class="h-10 w-auto">
            </div>
            <a href="{{ url('/') }}" class="back-btn">
                <span>العودة للرئيسية</span>
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
        </div>
    </header>

    @php
        $catIcons = [
            'sports' => ['emoji' => '⚽', 'label' => 'رياضي', 'cls' => 'badge-sports'],
            'culture' => ['emoji' => '📚', 'label' => 'ثقافي', 'cls' => 'badge-culture'],
            'achievement' => ['emoji' => '🏆', 'label' => 'إنجاز', 'cls' => 'badge-achievement'],
            'general' => ['emoji' => '📰', 'label' => 'عام', 'cls' => 'badge-general'],
        ];
        $info = $catIcons[$news->category] ?? $catIcons['general'];
    @endphp

    <div class="main-container">
        
        <!-- Main Content -->
        <main class="article-main">
            <div class="article-header">
                <div class="category-badge {{ $info['cls'] }}">
                    <span>{{ $info['emoji'] }}</span>
                    <span>{{ $info['label'] }}</span>
                </div>
                
                <h1 class="article-title">{{ $news->title }}</h1>
                
                <div class="article-meta">
                    <div class="meta-item">
                        <i class="fas fa-university text-gold"></i>
                        <span>{{ $news->center->name ?? 'المركز العام' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="far fa-clock text-gray-400"></i>
                        <span>{{ $news->published_at ? $news->published_at->format('Y/m/d h:i A') : $news->created_at->format('Y/m/d h:i A') }}</span>
                    </div>
                    <div class="meta-item mr-auto">
                        <i class="far fa-eye text-gray-400"></i>
                        <span>{{ rand(120, 950) }} مشاهدة</span> <!-- Mock views count for news feel -->
                    </div>
                </div>
            </div>

            @if($news->cover_image)
                <img src="{{ asset('storage/' . $news->cover_image) }}" alt="{{ $news->title }}" class="article-cover">
            @else
                <div style="height:250px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; margin-top:1.5rem;">
                    <i class="fas fa-newspaper text-gray-300" style="font-size:4rem"></i>
                </div>
            @endif

            <div class="article-body">
                {{ $news->body }}
            </div>
            
            @if($news->gallery && is_array($news->gallery) && count($news->gallery) > 0)
                <div class="gallery-grid">
                    @foreach($news->gallery as $img)
                        <a href="{{ asset('storage/' . $img) }}" target="_blank">
                            <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image" class="gallery-img">
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Engagement -->
            <div class="engagement-bar">
                @auth
                    <form action="{{ route('news.like', $news) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="action-btn {{ $isLiked ? 'liked' : '' }}">
                            <i class="{{ $isLiked ? 'fas' : 'far' }} fa-heart"></i>
                            <span>{{ $likesCount }} إعجاب</span>
                        </button>
                    </form>
                @else
                    <div class="action-btn" onclick="alert('يرجى تسجيل الدخول للإعجاب')">
                        <i class="far fa-heart"></i>
                        <span>{{ $likesCount }} إعجاب</span>
                    </div>
                @endauth
                
                <div class="action-btn">
                    <i class="far fa-comment"></i>
                    <span>{{ count($news->comments) }} تعليق</span>
                </div>
                
                <button class="action-btn mr-auto" onclick="navigator.share({title: '{{ $news->title }}', url: window.location.href})">
                    <i class="fas fa-share-alt"></i>
                    <span>مشاركة</span>
                </button>
            </div>

            <!-- Comments -->
            <div class="comments-section" id="comments">
                <h3 class="section-title">
                    <i class="fas fa-comments text-gold"></i>
                    التعليقات
                </h3>

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="comments-list">
                    @forelse($news->comments as $comment)
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="comment-content">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="comment-author">{{ $comment->user->name }}</div>
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if(auth()->id() === $comment->user_id || (auth()->check() && auth()->user()->can('manage-news')))
                                        <form action="{{ route('news.comments.delete', $comment) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف التعليق؟')">
                                            @csrf
                                            <button type="submit" class="text-red-400 hover:text-red-600 p-1">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="comment-text">
                                    {{ $comment->content }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 font-almarai">
                            <i class="far fa-comment-dots text-3xl mb-2 block"></i>
                            لا توجد تعليقات حتى الآن. كن أول من يعلق!
                        </div>
                    @endforelse
                </div>

                @auth
                    <div class="comment-form-container">
                        <form action="{{ route('news.comments.add', $news) }}" method="POST">
                            @csrf
                            <textarea name="content" class="comment-input" placeholder="اكتب تعليقك هنا..." required></textarea>
                            @error('content')
                                <p class="text-red-500 text-sm mb-3">{{ $message }}</p>
                            @enderror
                            <div class="text-left">
                                <button type="submit" class="btn-submit">
                                    <i class="fas fa-paper-plane ml-1"></i> إرسال التعليق
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="login-prompt mt-8">
                        يرجى <a href="{{ route('login') }}">تسجيل الدخول</a> للمشاركة والتعليق على الخبر.
                    </div>
                @endauth
            </div>
        </main>

        <!-- Sidebar -->
        <aside class="sidebar">
            @if(isset($related) && count($related) > 0)
                <div class="widget">
                    <h3 class="widget-title">أخبار ذات صلة</h3>
                    <div class="related-list">
                        @foreach($related as $rel)
                            <a href="{{ route('news.public-show', $rel) }}" class="related-card">
                                @if($rel->cover_image)
                                    <img src="{{ asset('storage/' . $rel->cover_image) }}" class="related-img">
                                @else
                                    <div class="related-img" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center;">
                                        <i class="fas fa-image text-gray-300"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="related-title">{{ Str::limit($rel->title, 60) }}</h4>
                                    <div class="text-xs text-gray-400 mt-2 font-almarai">
                                        <i class="far fa-clock"></i> {{ $rel->published_at ? $rel->published_at->format('Y/m/d') : '' }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="widget" style="background: linear-gradient(135deg, var(--navy), #002a50); color: white; border:none;">
                <div class="text-center py-4">
                    <i class="fas fa-university text-gold text-4xl mb-4"></i>
                    <h4 class="font-bold text-lg mb-2">منصة الإسكان الطلابي</h4>
                    <p class="text-sm text-gray-300 font-almarai mb-4 line-height-1.6">
                        النظام الموحد لإدارة الشؤون الإدارية والمالية والخدمات الطلابية
                    </p>
                    <a href="{{ route('login') }}" class="inline-block bg-gold text-navy font-bold px-6 py-2 rounded-full hover:bg-white transition-colors text-sm">
                        دخول المنصة
                    </a>
                </div>
            </div>
        </aside>

    </div>

    <footer style="background: white; border-top: 1px solid #e2e8f0; padding: 2rem 0; text-align: center; margin-top: 4rem;">
        <p style="color: #64748b; font-size: 0.9rem; font-family: 'Almarai', sans-serif;">
            &copy; {{ date('Y') }} نظام إدارة السكن الطلابي — جمعية رعاية طالب العلم
        </p>
    </footer>

</body>
</html>
