<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);

        $query = Post::with(['author', 'category', 'tags'])->withTrashed();

        // If author role without edit-any permission, only see own posts
        $user = Auth::user();
        if ($user->hasRole('author') && !$user->isEditor()) {
            $query->where('author_id', $user->id);
        }

        // Filter: Status
        if ($request->filled('status')) {
            if ($request->status === 'trash') {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed()->where('status', $request->status);
            }
        } else {
            $query->withoutTrashed();
        }

        // Filter: Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter: Search Keyword
        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword)
                    ->orWhere('excerpt', 'like', $keyword);
            });
        }

        $posts = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create(): View
    {
        Gate::authorize('create', Post::class);

        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:posts,slug'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'string', 'in:news,tutorial,guide,article'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        // Authorization check for direct publishing
        if ($validated['status'] === 'published' && !Auth::user()->can('publish', Post::class)) {
            $validated['status'] = 'draft';
        }

        // Auto-generate slug if empty
        if (empty($validated['slug'])) {
            $slugBase = Str::slug($validated['title']);
            $slug = $slugBase;
            $counter = 1;
            while (Post::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        // If published and no publish timestamp specified, use now
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $validated['author_id'] = Auth::id();

        $post = Post::create($validated);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        }

        ActivityLogger::log('create', $post, ['title' => $post->title, 'status' => $post->status]);

        return redirect()->route('admin.posts.index')->with('success', "Article '{$post->title}' created successfully.");
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function preview(Post $post): View
    {
        Gate::authorize('view', $post);

        return view('admin.posts.preview', compact('post'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:191', Rule::unique('posts', 'slug')->ignore($post->id)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', 'string', 'in:news,tutorial,guide,article'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,published,archived'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        // Restrict author from self-publishing unless authorized
        if ($validated['status'] === 'published' && $post->status !== 'published' && !Auth::user()->can('publish', $post)) {
            $validated['status'] = 'draft';
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = $post->published_at ?? now();
        }

        $post->update($validated);

        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }

        ActivityLogger::log('update', $post, ['title' => $post->title, 'status' => $post->status]);

        return redirect()->route('admin.posts.index')->with('success', "Article '{$post->title}' updated successfully.");
    }

    public function publish(Post $post): RedirectResponse
    {
        Gate::authorize('publish', $post);

        $post->update([
            'status' => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);

        ActivityLogger::log('publish', $post, ['title' => $post->title]);

        return back()->with('success', "Article '{$post->title}' published.");
    }

    public function unpublish(Post $post): RedirectResponse
    {
        Gate::authorize('publish', $post);

        $post->update([
            'status' => 'draft',
        ]);

        ActivityLogger::log('unpublish', $post, ['title' => $post->title]);

        return back()->with('success', "Article '{$post->title}' moved to draft.");
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $title = $post->title;
        $post->delete();

        ActivityLogger::log('delete', $post, ['title' => $title]);

        return redirect()->route('admin.posts.index')->with('success', "Article '{$title}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        Gate::authorize('restore', $post);

        $post->restore();

        ActivityLogger::log('restore', $post, ['title' => $post->title]);

        return back()->with('success', "Article '{$post->title}' restored successfully.");
    }
}
