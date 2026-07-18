<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $centerId = auth()->user()->center_id;
        $query = News::where('center_id', $centerId);

        if (!auth()->user()->can('manage-news')) {
            $query->where('is_published', true);
        }

        $news = $query->latest()->paginate(12);
        return view('social.news.index', compact('news'));
    }

    public function create()
    {
        return view('social.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'category' => 'required|in:general,sports,culture,achievement',
            'cover_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
            'video_url' => 'nullable|url',
            'video_file' => 'nullable|mimes:mp4,mov,ogg,qt|max:20480', // 20MB max
            'is_published' => 'nullable|boolean',
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

        $isPublished = $request->boolean('is_published');

        News::create([
            'center_id' => auth()->user()->center_id,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
            'category' => $validated['category'],
            'video_url' => $validated['video_url'],
            'video_path' => $videoPath,
            'cover_image' => $coverPath,
            'gallery' => count($galleryPaths) ? $galleryPaths : null,
            'is_published' => $isPublished,
            'published_at' => $isPublished ? now() : null,
        ]);

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
            'category' => 'required|in:general,sports,culture,achievement',
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

        $isPublished = $request->boolean('is_published');
        $validated['is_published'] = $isPublished;
        if ($isPublished && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return redirect()->route('news.show', $news)->with('success', 'تم تحديث الخبر.');
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
        $news->update([
            'is_published' => !$news->is_published,
            'published_at' => !$news->is_published ? now() : $news->published_at,
        ]);
        return back()->with('success', $news->is_published ? 'تم نشر الخبر.' : 'تم إلغاء نشر الخبر.');
    }

    public function publicShow(News $news)
    {
        // Only published news accessible publicly
        if (!$news->is_published) {
            abort(404);
        }
        // Related news from same center (excluding current)
        $related = News::where('center_id', $news->center_id)
            ->where('id', '!=', $news->id)
            ->where('is_published', true)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('social.news.public_show', compact('news', 'related'));
    }

    // Public feed for login page (latest published news from all centers)
    public function publicFeed()
    {
        $news = News::where('is_published', true)
            ->with('center:id,name')
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
            'content' => $request->content,
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
}
