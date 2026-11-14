@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title)
@section('page_title', 'Edit Page')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href="{{ route('admin.pages.index') }}" class="small text-muted text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Back to Static Pages
        </a>
        <h3 class="fw-bold mt-2 mb-0">Edit Page: {{ $page->title }}</h3>
    </div>
    <a href="{{ url('/' . $page->slug) }}" target="_blank" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-box-arrow-up-right me-1"></i> View Live Page
    </a>
</div>

<form action="{{ route('admin.pages.update', $page) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small" for="title">Page Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="slug">Public URL Slug</label>
                    <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}" {{ $page->is_system ? 'readonly' : '' }} required>
                    @if($page->is_system)
                    <div class="form-text small text-info"><i class="bi bi-info-circle me-1"></i> System slugs are locked to prevent breaking canonical public routes.</div>
                    @endif
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small" for="content">Page Body Content (HTML / Rich Format) <span class="text-danger">*</span></label>
                    <textarea name="content" id="content" rows="16" class="form-control font-monospace @error('content') is-invalid @enderror" required>{{ old('content', $page->content) }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- SEO Settings Box -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-search me-1"></i> SEO & Meta Information</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="seo_title">SEO Title Tag</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control @error('seo_title') is-invalid @enderror" value="{{ old('seo_title', $page->seo_title) }}">
                    @error('seo_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="meta_description">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="form-control @error('meta_description') is-invalid @enderror">{{ old('meta_description', $page->meta_description) }}</textarea>
                    @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="canonical_url">Canonical URL</label>
                        <input type="url" name="canonical_url" id="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror" value="{{ old('canonical_url', $page->canonical_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold" for="og_image">Social Sharing Image</label>
                        <input type="text" name="og_image" id="og_image" class="form-control @error('og_image') is-invalid @enderror" value="{{ old('og_image', $page->og_image) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-toggles me-1"></i> Publication Status</h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold" for="status">Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>Published (Active on website)</option>
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Draft (Offline)</option>
                    </select>
                </div>

                <div class="small text-muted mb-3">
                    <div>Last Updated: <span class="font-monospace text-body">{{ $page->updated_at->format('Y-m-d H:i') }}</span></div>
                    <div>Page Key: <span class="font-monospace text-body">{{ $page->system_key ?? 'Custom' }}</span></div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</form>
@endsection
