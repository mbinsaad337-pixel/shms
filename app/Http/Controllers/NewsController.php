<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Center;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    // PUBLIC LANDING PAGE
    // ─────────────────────────────────────────────────────────────────

    public function welcome()
    {
        $centers = Center::where('is_active', true)
            ->withCount(['students' => fn ($query) => $query->where('status', 'residing')])
            ->orderBy('name')
            ->get();

        $allCenters = Center::orderBy('name')->get(['id', 'name']);

        // Recent published news
        $recentNews = News::where('is_published', true)
            ->with('center:id,name')
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // Stats
        $stats = [
            'centers'  => Center::where('is_active', true)->count(),
            'students' => Student::where('status', 'residing')->count(),
            'graduates'=> Student::where('status', 'graduated')->count(),
            'news'     => News::where('is_published', true)->count(),
        ];

        return view('welcome', compact('centers', 'allCenters', 'recentNews', 'stats'));
    }

    public function publicCenters()
    {
        $centers = Center::where('is_active', true)
            ->withCount(['students', 'rooms'])
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'phone', 'email', 'logo']);

        return response()->json($centers);
    }

    public function publicNewsFilter(Request $request)
    {
        $query = News::where('is_published', true)
            ->with('center:id,name')
            ->withCount(['likes', 'comments']);

        if ($request->filled('center_id')) {
            $query->where('center_id', $request->center_id);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $news = $query->latest('published_at')
            ->paginate(9, ['id', 'center_id', 'title', 'body', 'cover_image', 'category', 'published_at']);

        return response()->json($news);
    }

    // ─────────────────────────────────────────────────────────────────
    // AUTHENTICATED NEWS MANAGEMENT
    // ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $user = auth()->user();
        $query = News::with(['center', 'creator']);

        if (!$user->hasAnyRole(['super-admin', 'media-officer', 'executive-manager'])) {
            // Center users see their center's news and general news
            $query->where(function($q) use ($user) {
                $q->where('center_id', $user->center_id)
                  ->orWhereNull('center_id');
            });
            if (!$user->can('manage-news')) {
                $query->where('is_published', true);
            }
        }

        $news = $query->latest()->paginate(12);
        return view('social.news.index', compact('news'));
    }

    public function pendingIndex()
    {
        // Media Officer Dashboard / Pending News List across all centers
        if (!auth()->user()->hasRole(['super-admin', 'media-officer'])) {
            abort(403, 'غير مصرح لك بالوصول للوحة اعتمادات الإعلام');
        }

        $query = News::query();
        if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'status')) {
            $query->where('status', 'pending')
                ->orWhere(function ($q) {
                    $q->where('is_published', false)->where('status', '!=', 'rejected');
                });
            $approvedCount = News::where('status', 'approved')->orWhere('is_published', true)->count();
            $rejectedCount = News::where('status', 'rejected')->count();
        } else {
            $query->where('is_published', false);
            $approvedCount = News::where('is_published', true)->count();
            $rejectedCount = 0;
        }

        $pendingNews = $query->with(['center', 'creator'])
            ->latest()
            ->paginate(12);

        return view('social.news.pending', compact('pendingNews', 'approvedCount', 'rejectedCount'));
    }

    public function create()
    {
        $centers = [];
        if (auth()->user()->hasAnyRole(['super-admin', 'media-officer'])) {
            $centers = \App\Models\Center::orderBy('name')->get(['id', 'name']);
        }
        return view('social.news.create', compact('centers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isPrivileged = $user->hasAnyRole(['super-admin', 'media-officer']);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category'    => 'required|in:general,sports,culture,achievement,jobs',
            'cover_image' => 'nullable|image|max:4096',
            'gallery.*'   => 'nullable|image|max:4096',
            'video_url'   => 'nullable|url',
            'video_file'  => 'nullable|mimes:mp4,mov,ogg,qt|max:20480',
            'is_published'=> 'nullable|boolean',
            'center_id'   => 'nullable|exists:centers,id',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('news/covers', 'public');
        }

        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('news/videos', 'public');
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $galleryPaths[] = $img->store('news/gallery', 'public');
            }
        }

        $canDirectPublish = $isPrivileged;

        $isPublishedRequest = $request->boolean('is_published');
        $isPublished = $canDirectPublish ? $isPublishedRequest : false;
        $status = $canDirectPublish && $isPublishedRequest ? 'approved' : 'pending';

        // Resolve center_id: privileged users pick from form (optional); others use their own center
        $centerId = $isPrivileged ? ($validated['center_id'] ?? null) : $user->center_id;

        $news = News::create([
            'center_id' => $centerId,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'body' => $validated['body'],
            'category' => $validated['category'],
            'video_url' => $validated['video_url'],
            'video_path' => $videoPath,
            'cover_image' => $coverPath,
            'gallery' => count($galleryPaths) ? $galleryPaths : null,
            'status' => $status,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

        if ($status === 'pending') {
            $this->notifyMediaOfficer($news);
            return redirect()->route('news.index')->with('success', 'تم تقديم الإعلان بنجاح وهو الآن في قائمة الانتظار للموافقة عليه من قبل مسؤول الإعلام.');
        }

        return redirect()->route('news.index')->with('success', 'تم نشر الخبر بنجاح.');
    }

    public function show(News $news)
    {
        return view('social.news.show', compact('news'));
    }

    public function edit(News $news)
    {
        return view('social.news.edit', compact('news'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'required|in:general,sports,culture,achievement,jobs',
            'cover_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480',
            'is_published' => 'nullable|boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($news->cover_image)
                Storage::disk('public')->delete($news->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('news/covers', 'public');
        }

        if ($request->hasFile('video_file')) {
            if ($news->video_path)
                Storage::disk('public')->delete($news->video_path);
            $validated['video_path'] = $request->file('video_file')->store('news/videos', 'public');
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = $news->gallery ?? [];
            foreach ($request->file('gallery') as $img) {
                $galleryPaths[] = $img->store('news/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $user = auth()->user();
        if (!$user->hasAnyRole(['super-admin', 'media-officer'])) {
            // Re-submit for pending approval if modified by center manager
            $validated['status'] = 'pending';
            $validated['is_published'] = false;
            $validated['rejection_reason'] = null;
        } else {
            $isPublished = $request->boolean('is_published');
            $validated['is_published'] = $isPublished;
            if ($isPublished) {
                $validated['status'] = 'approved';
                if (!$news->published_at) {
                    $validated['published_at'] = now();
                }
            }
        }

        $news->update($validated);

        if ($news->status === 'pending') {
            $this->notifyMediaOfficer($news);
            return redirect()->route('news.show', $news)->with('success', 'تم تحديث الإعلان وإرساله لقائمة الانتظار لمراجعة مسؤول الإعلام.');
        }

        return redirect()->route('news.show', $news)->with('success', 'تم تحديث الخبر.');
    }

    public function approve(News $news)
    {
        if (!auth()->user()->hasRole(['super-admin', 'media-officer'])) {
            abort(403);
        }

        $news->update([
            'status' => 'approved',
            'is_published' => true,
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'تم اعتماد ونشر الإعلان/الخبر بنجاح.');
    }

    public function reject(Request $request, News $news)
    {
        if (!auth()->user()->hasRole(['super-admin', 'media-officer'])) {
            abort(403);
        }

        $request->validate(['reason' => 'nullable|string|max:500']);

        $news->update([
            'status' => 'rejected',
            'is_published' => false,
            'rejection_reason' => $request->input('reason'),
        ]);

        return back()->with('success', 'تم رفض نشر الإعلان.');
    }

    public function destroy(News $news)
    {
        if ($news->cover_image)
            Storage::disk('public')->delete($news->cover_image);
        if ($news->gallery) {
            foreach ($news->gallery as $img)
                Storage::disk('public')->delete($img);
        }
        $news->delete();

        return redirect()->route('news.index')->with('success', 'تم حذف الخبر.');
    }

    public function togglePublish(News $news)
    {
        $newPublishState = !$news->is_published;
        $news->update([
            'is_published' => $newPublishState,
            'status' => $newPublishState ? 'approved' : 'pending',
            'published_at' => $newPublishState ? now() : $news->published_at,
        ]);
        return back()->with('success', $news->is_published ? 'تم نشر الخبر.' : 'تم إلغاء نشر الخبر وتوجيهه للانتظار.');
    }

    public function publicShow(News $news)
    {
        if (!$news->is_published) {
            abort(404);
        }
        $related = News::where('center_id', $news->center_id)
            ->where('id', '!=', $news->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        $news->load(['comments.user', 'likes']);
        $likesCount = $news->likes->count();
        $isLiked = auth()->check() ? $news->isLikedBy(auth()->user()) : false;

        return view('social.news.public_show', compact('news', 'related', 'likesCount', 'isLiked'));
    }

    public function publicFeed()
    {
        $news = News::where('is_published', true)
            ->with('center:id,name')
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->take(6)
            ->get(['id', 'center_id', 'title', 'body', 'cover_image', 'category', 'published_at']);

        return response()->json($news);
    }

    public function toggleLike(News $news)
    {
        $like = \App\Models\NewsLike::where('news_id', $news->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete();
            return back()->with('success', 'تم إلغاء الإعجاب.');
        }

        \App\Models\NewsLike::create([
            'news_id' => $news->id,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'تم تسجيل الإعجاب.');
    }

    public function addComment(Request $request, News $news)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        \App\Models\NewsComment::create([
            'news_id' => $news->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'تم إضافة التعليق بنجاح.');
    }

    public function deleteComment(\App\Models\NewsComment $comment)
    {
        if (auth()->id() !== $comment->user_id && !auth()->user()->can('manage-news')) {
            abort(403);
        }

        $comment->delete();
        return back()->with('success', 'تم حذف التعليق.');
    }

    private function notifyMediaOfficer(News $news)
    {
        $centerName = auth()->user()->center ? auth()->user()->center->name : 'المركز';

        // Auto-ensure role exists so Spatie never throws missing role exception
        try {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'media-officer', 'guard_name' => 'web']);
            $mediaOfficer = User::role('media-officer')->whereNotNull('phone')->first();
        } catch (\Throwable $e) {
            $mediaOfficer = null;
        }

        $phone = $mediaOfficer ? preg_replace('/[^0-9]/', '', $mediaOfficer->phone) : '';

        $message = "📢 *إعلان جديد بانتظار الاعتماد*\n\n"
            . "يقوم مركز *{$centerName}* بطلب نشر إعلان جديد في النظام:\n"
            . "📌 *عنوان الخبر:* {$news->title}\n"
            . "👤 *المُرسل:* " . auth()->user()->name . "\n\n"
            . "يرجى الدخول للوحة تحكم مسؤول الإعلام في النظام للاطلاع عليه والبت فيه بالموافقة أو الرفض.";

        if ($phone) {
            $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
        } else {
            $whatsappUrl = "https://api.whatsapp.com/send?text=" . urlencode($message);
        }

        session()->flash('whatsapp_url', $whatsappUrl);
    }
}
