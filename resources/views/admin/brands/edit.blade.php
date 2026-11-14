@extends('layouts.admin')

@section('title', 'Edit Brand - ' . $brand->name)
@section('page_title', 'Edit Brand')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Brands
        </a>
        <h4 class="fw-bold mb-1">Edit Brand: {{ $brand->name }}</h4>
        <p class="text-muted small mb-0">Contains {{ $brand->devices_count ?? 0 }} registered device(s)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('public.brand.detail', $brand->slug) }}" target="_blank" class="btn btn-outline-secondary rounded-pill btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Public Page
        </a>
        <a href="{{ route('admin.devices.index', ['brand_id' => $brand->id]) }}" class="btn btn-outline-primary rounded-pill btn-sm">
            <i class="bi bi-phone me-1"></i> View Devices
        </a>
    </div>
</div>

<form action="{{ route('admin.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Brand Identity</h5>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Brand Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $brand->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label fw-semibold">URL Slug</label>
                    <div class="input-group">
                        <span class="input-group-text bg-body-tertiary">/brands/</span>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $brand->slug) }}">
                    </div>
                    @error('slug')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label fw-semibold">Short Summary / Tagline</label>
                    <textarea name="short_description" id="short_description" rows="2" class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description', $brand->short_description) }}</textarea>
                    @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="full_description" class="form-label fw-semibold">Full Brand Profile & History</label>
                    <textarea name="full_description" id="full_description" rows="5" class="form-control @error('full_description') is-invalid @enderror">{{ old('full_description', $brand->full_description) }}</textarea>
                    @error('full_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="country_of_origin" class="form-label fw-semibold">Country of Origin</label>
                        <input type="text" name="country_of_origin" id="country_of_origin" class="form-control @error('country_of_origin') is-invalid @enderror" value="{{ old('country_of_origin', $brand->country_of_origin) }}">
                        @error('country_of_origin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="website_url" class="form-label fw-semibold">Official Website</label>
                        <input type="url" name="website_url" id="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url', $brand->website_url) }}">
                        @error('website_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">SEO & Metadata</h5>
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">SEO Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $brand->seo_title) }}">
                </div>
                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" class="form-control">{{ old('meta_description', $brand->meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url', $brand->canonical_url) }}">
                </div>
            </div>
        </div>

        <!-- Sidebar Options Column -->
        <div class="col-lg-4">
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Publishing Status</h5>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', $brand->status) === 'published' ? 'selected' : '' }}>Published (Visible to public)</option>
                        <option value="draft" {{ old('status', $brand->status) === 'draft' ? 'selected' : '' }}>Draft (Admin only)</option>
                        <option value="archived" {{ old('status', $brand->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="sort_order" class="form-label fw-semibold">Sort Order</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" value="{{ old('sort_order', $brand->sort_order) }}" min="0">
                    <small class="text-muted">Lower numbers appear first in listings.</small>
                </div>

                <hr>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill">
                        <i class="bi bi-check-lg me-1"></i> Update Brand
                    </button>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary rounded-pill">Cancel</a>
                </div>
            </div>

            <!-- Brand Logo Upload -->
            <div class="mrh-card p-4">
                <h5 class="fw-bold mb-3">Brand Logo</h5>
                @if($brand->logo)
                    <div class="mb-3 p-3 bg-white border rounded text-center">
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                        <div class="form-check mt-2 text-start">
                            <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogo">
                            <label class="form-check-label text-danger small" for="removeLogo">Remove current logo</label>
                        </div>
                    </div>
                @endif
                <div class="mb-3">
                    <label for="logo" class="form-label small fw-semibold">{{ $brand->logo ? 'Replace Logo' : 'Upload Logo' }}</label>
                    <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                    <small class="text-muted d-block mt-1">PNG, WEBP, or SVG. Max 2MB.</small>
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
