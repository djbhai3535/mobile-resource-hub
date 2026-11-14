@extends('layouts.admin')

@section('title', 'Edit Article: ' . $post->title)
@section('page_title', 'Edit Article')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('admin.posts.index') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back to Articles
        </a>
        <h3 class="fw-bold mt-2 mb-0">Edit Article</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.posts.preview', $post) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-eye me-1"></i> Preview
        </a>
    </div>
</div>

<form action="{{ route('admin.posts.update', $post) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $post->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="slug">URL Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug', $post->slug) }}" required>
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="excerpt">Excerpt / Summary</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror">{{ old('excerpt', $post->excerpt) }}</textarea>
                    @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="content">Full Article Content <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="15" class="form-control font-monospace @error('content') is-invalid @enderror" required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SEO Settings Box -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-1"></i> Search Engine Optimization (SEO)</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="seo_title">SEO Title Tag</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $post->seo_title) }}">
                    @error('seo_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="meta_description">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $post->meta_description) }}</textarea>
                    @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="canonical_url">Canonical URL Override</label>
                        <input type="url" name="canonical_url" id="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror" value="{{ old('canonical_url', $post->canonical_url) }}">
                        @error('canonical_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="og_image">Open Graph Image URL</label>
                        <input type="text" name="og_image" id="og_image" class="form-control @error('og_image') is-invalid @enderror" value="{{ old('og_image', $post->og_image) }}">
                        @error('og_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Meta Sidebar Column -->
        <div class="col-lg-4">
            <!-- Publishing Status Box -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-send me-1"></i> Publication Status</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="status">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        @can('publish', $post)
                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                        @endcan
                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="published_at">Publication Date</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}">
                    @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="type">Article Format</label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="news" {{ old('type', $post->type) === 'news' ? 'selected' : '' }}>News Story</option>
                        <option value="tutorial" {{ old('type', $post->type) === 'tutorial' ? 'selected' : '' }}>Repair / Flashing Tutorial</option>
                        <option value="guide" {{ old('type', $post->type) === 'guide' ? 'selected' : '' }}>Hardware Guide</option>
                        <option value="article" {{ old('type', $post->type) === 'article' ? 'selected' : '' }}>Standard Article</option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="small text-muted mb-3">
                    <div>Author: <span class="fw-semibold text-body">{{ $post->author?->name ?? 'System' }}</span></div>
                    <div>Created: <span class="font-monospace">{{ $post->created_at->format('Y-m-d H:i') }}</span></div>
                    <div>Updated: <span class="font-monospace">{{ $post->updated_at->format('Y-m-d H:i') }}</span></div>
                </div>

                <hr class="my-3">

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Update Article
                </button>
            </div>

            <!-- Taxonomy Box -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-1"></i> Categorization</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="category_id">Primary Category</label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Select Category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $post->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $selectedTags = old('tags', $post->tags->pluck('id')->toArray());
                @endphp
                <div class="mb-3">
                    <label class="form-label small fw-bold">Tags</label>
                    <div class="d-flex flex-wrap gap-2 border p-2 rounded-3 bg-body-tertiary" style="max-height: 180px; overflow-y: auto;">
                        @forelse($tags as $tag)
                        <div class="form-check form-check-inline mb-1">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                        @empty
                        <span class="text-muted small">No tags defined.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Featured Image Box -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-card-image me-1"></i> Featured Image</h5>

                <div class="mb-2">
                    <input type="text" name="featured_image" id="featured_image" class="form-control form-control-sm @error('featured_image') is-invalid @enderror" value="{{ old('featured_image', $post->featured_image) }}">
                    <div class="form-text small">Enter image path or copy from <a href="{{ route('admin.media.index') }}" target="_blank">Media Library</a>.</div>
                    @error('featured_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @if($post->featured_image)
                <div class="mt-2 text-center">
                    <img src="{{ $post->featured_image }}" alt="Featured preview" class="img-fluid rounded border" style="max-height: 120px;">
                </div>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection
