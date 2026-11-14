@extends('layouts.admin')

@section('title', 'New Article')
@section('page_title', 'Create Article')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.posts.index') }}" class="small text-muted text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Back to Articles
    </a>
    <h3 class="fw-bold mt-2 mb-0">Create New Article</h3>
</div>

<form action="{{ route('admin.posts.store') }}" method="POST">
    @csrf

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small" for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" placeholder="Enter article title..." value="{{ old('title') }}" required autofocus>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="slug">URL Slug <span class="text-muted fw-normal">(optional, auto-generated from title)</span></label>
                    <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" placeholder="e.g. samsung-oneui-7-features" value="{{ old('slug') }}">
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="excerpt">Excerpt / Summary</label>
                    <textarea name="excerpt" id="excerpt" rows="3" class="form-control @error('excerpt') is-invalid @enderror" placeholder="Brief 1-2 sentence lead summary for catalog cards and social shares...">{{ old('excerpt') }}</textarea>
                    @error('excerpt')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="content">Full Article Content (HTML / Markdown supported) <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="15" class="form-control font-monospace @error('content') is-invalid @enderror" placeholder="Write full article body..." required>{{ old('content') }}</textarea>
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
                    <input type="text" name="seo_title" id="seo_title" class="form-control @error('seo_title') is-invalid @enderror" placeholder="Custom page title for search engines..." value="{{ old('seo_title') }}">
                    @error('seo_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="meta_description">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="form-control @error('meta_description') is-invalid @enderror" placeholder="Concise summary for SERP snippet (max 160 characters)...">{{ old('meta_description') }}</textarea>
                    @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="canonical_url">Canonical URL Override</label>
                        <input type="url" name="canonical_url" id="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror" placeholder="https://example.com/original-article" value="{{ old('canonical_url') }}">
                        @error('canonical_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="og_image">Open Graph Image URL</label>
                        <input type="text" name="og_image" id="og_image" class="form-control @error('og_image') is-invalid @enderror" placeholder="/storage/media/og-banner.webp" value="{{ old('og_image') }}">
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
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        @can('publish', App\Models\Post::class)
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        @endcan
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="published_at">Publication Date</label>
                    <input type="datetime-local" name="published_at" id="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at') }}">
                    <div class="form-text small">Leave blank to publish immediately upon approval.</div>
                    @error('published_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="type">Article Format</label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror">
                        <option value="news" {{ old('type', 'news') === 'news' ? 'selected' : '' }}>News Story</option>
                        <option value="tutorial" {{ old('type') === 'tutorial' ? 'selected' : '' }}>Repair / Flashing Tutorial</option>
                        <option value="guide" {{ old('type') === 'guide' ? 'selected' : '' }}>Hardware Guide</option>
                        <option value="article" {{ old('type') === 'article' ? 'selected' : '' }}>Standard Article</option>
                    </select>
                    @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-3">

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Save Article
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
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Tags</label>
                    <div class="d-flex flex-wrap gap-2 border p-2 rounded-3 bg-body-tertiary" style="max-height: 180px; overflow-y: auto;">
                        @forelse($tags as $tag)
                        <div class="form-check form-check-inline mb-1">
                            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
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
                    <input type="text" name="featured_image" id="featured_image" class="form-control form-control-sm @error('featured_image') is-invalid @enderror" placeholder="/storage/media/cover.webp" value="{{ old('featured_image') }}">
                    <div class="form-text small">Enter image path or copy from <a href="{{ route('admin.media.index') }}" target="_blank">Media Library</a>.</div>
                    @error('featured_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
