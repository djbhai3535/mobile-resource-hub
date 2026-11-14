@extends('layouts.admin')

@section('title', 'Manage Posts')
@section('page_title', 'Articles & Guides')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Articles & Guides Management</h4>
        <p class="text-muted small mb-0">Create, edit, schedule, and publish editorial content and tutorials</p>
    </div>
    @can('create', App\Models\Post::class)
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Article
    </a>
    @endcan
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.posts.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search by title or slug..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses (Active)</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                <option value="trash" {{ request('status') === 'trash' ? 'selected' : '' }}>Trash (Deleted)</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<!-- Posts Table -->
<div class="mrh-card p-0 overflow-hidden">
    @if($posts->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
        <h5>No articles found</h5>
        <p class="small mb-3">Adjust your search filters or create a new article to get started.</p>
        @can('create', App\Models\Post::class)
        <a href="{{ route('admin.posts.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">Create First Article</a>
        @endcan
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 280px;">Title & Slug</th>
                    <th>Category</th>
                    <th>Tags</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Published At</th>
                    <th class="text-end" style="min-width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posts as $post)
                <tr class="{{ $post->trashed() ? 'table-danger-subtle' : '' }}">
                    <td>
                        <div class="fw-bold text-body">{{ $post->title }}</div>
                        <div class="small text-muted font-monospace">{{ $post->slug }}</div>
                    </td>
                    <td>
                        @if($post->category)
                        <span class="badge bg-body-tertiary text-body border">{{ $post->category->name }}</span>
                        @else
                        <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        @forelse($post->tags->take(3) as $tag)
                        <span class="badge bg-secondary-subtle text-secondary small me-1">#{{ $tag->name }}</span>
                        @empty
                        <span class="text-muted small">—</span>
                        @endforelse
                    </td>
                    <td class="small">{{ $post->author?->name ?? 'System' }}</td>
                    <td>
                        @if($post->trashed())
                        <span class="badge bg-danger">In Trash</span>
                        @elseif($post->status === 'published')
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                        @elseif($post->status === 'draft')
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Archived</span>
                        @endif
                    </td>
                    <td class="small text-muted font-monospace">
                        {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '—' }}
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            @if($post->trashed())
                            @can('restore', $post)
                            <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success" title="Restore Article">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </form>
                            @endcan
                            @else
                            <a href="{{ route('admin.posts.preview', $post) }}" class="btn btn-outline-secondary" title="Preview Article" target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('update', $post)
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline-primary" title="Edit Article">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan

                            @can('publish', $post)
                            @if($post->status === 'published')
                            <form action="{{ route('admin.posts.unpublish', $post) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning" title="Move to Draft">
                                    <i class="bi bi-pause-circle"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('admin.posts.publish', $post) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-success" title="Publish Article">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                            @endif
                            @endcan

                            @can('delete', $post)
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Move article to trash?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Move to Trash">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($posts->hasPages())
    <div class="p-3 border-top d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
