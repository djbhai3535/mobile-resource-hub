@extends('layouts.admin')

@section('title', 'Upload 3D Mobile Showcase Video')
@section('page_title', 'Upload 3D Showcase Video')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="mrh-card p-4 p-md-5 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-badge-lumzy" style="background: rgba(37, 99, 235, 0.12); color: #2563EB; font-size: 1.5rem; width: 48px; height: 48px;">
                        <i class="bi bi-badge-3d"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Upload 3D Showcase Video</h4>
                        <p class="text-muted small mb-0">Add a smartphone video render to the homepage 3D carousel</p>
                    </div>
                </div>
                <a href="{{ route('admin.showcase.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    &larr; Back to List
                </a>
            </div>

            <form action="{{ route('admin.showcase.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Section 1: Video File & Live Preview -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-film text-primary"></i> 3D Video File <span class="text-danger">*</span>
                    </h5>
                    <p class="text-muted small mb-3">
                        Upload your 3D smartphone rotation or showcase video directly from your PC. Supported formats: MP4, WebM (max 50MB).
                    </p>

                    <div class="mb-3">
                        <input 
                            type="file" 
                            name="video" 
                            id="videoInput" 
                            class="form-control @error('video') is-invalid @enderror" 
                            accept="video/mp4,video/webm" 
                            required 
                            onchange="previewVideoFile(this)"
                        >
                        <div class="form-text text-muted small">
                            Recommended aspect ratio: 16:9 or 21:9 landscape; smooth 3D smartphone rotation or floating render.
                        </div>
                        @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Client-Side Video Preview Box -->
                    <div id="videoPreviewContainer" class="d-none mt-3">
                        <span class="text-muted small fw-semibold d-block mb-1">Video Player Preview:</span>
                        <div class="rounded-3 overflow-hidden border shadow-sm bg-dark" style="max-width: 480px; max-height: 270px;">
                            <video id="videoPreview" class="w-100" controls muted playsinline style="max-height: 270px; object-fit: contain;"></video>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Optional Poster Image -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-image text-warning"></i> Video Poster / Thumbnail <span class="badge bg-secondary-subtle text-secondary small fw-normal ms-2">Optional</span>
                    </h5>
                    <p class="text-muted small mb-3">
                        Thumbnail displayed while the video loads or on connections where autoplay is deferred.
                    </p>

                    <div class="mb-3">
                        <input 
                            type="file" 
                            name="poster" 
                            id="posterInput" 
                            class="form-control @error('poster') is-invalid @enderror" 
                            accept=".jpg,.jpeg,.png,.webp,.svg"
                            onchange="previewPosterFile(this)"
                        >
                        <div class="form-text text-muted small">Max size: 5MB (JPG, PNG, WebP).</div>
                        @error('poster')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="posterPreviewContainer" class="d-none mt-2">
                        <span class="text-muted small fw-semibold d-block mb-1">Poster Preview:</span>
                        <div class="p-1 bg-white rounded border d-inline-block shadow-sm">
                            <img id="posterPreview" src="#" alt="Poster Preview" style="max-width: 240px; max-height: 135px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Descriptive Content -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-fonts text-success"></i> Content &amp; Meta
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="title">Title <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title" 
                            class="form-control @error('title') is-invalid @enderror" 
                            value="{{ old('title') }}" 
                            placeholder="e.g. Next-Gen Titanium Flagship" 
                            required
                        >
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" for="subtitle">Subtitle / Highlight</label>
                        <input 
                            type="text" 
                            name="subtitle" 
                            id="subtitle" 
                            class="form-control @error('subtitle') is-invalid @enderror" 
                            value="{{ old('subtitle') }}" 
                            placeholder="e.g. Snapdragon 8 Gen 3 &bull; 200MP Quad Camera Array"
                        >
                        @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold" for="brand_name">Brand Name <span class="text-muted small fw-normal">(Optional)</span></label>
                            <input 
                                type="text" 
                                name="brand_name" 
                                id="brand_name" 
                                class="form-control @error('brand_name') is-invalid @enderror" 
                                value="{{ old('brand_name') }}" 
                                placeholder="e.g. Samsung, Apple, Xiaomi"
                            >
                            @error('brand_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label fw-bold" for="sort_order">Display Order</label>
                            <input 
                                type="number" 
                                name="sort_order" 
                                id="sort_order" 
                                class="form-control @error('sort_order') is-invalid @enderror" 
                                value="{{ old('sort_order', 0) }}" 
                                min="0" 
                                max="999"
                            >
                            <div class="form-text text-muted small">Lower numbers display first in the carousel.</div>
                            @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Playback Controls & Status -->
                <div class="mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-info"></i> Playback Configuration
                    </h5>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="is_active">Active on Homepage</label>
                                <div class="text-muted small ps-5">Video will appear in public showcase carousel.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="autoplay" id="autoplay" value="1" {{ old('autoplay', true) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="autoplay">Autoplay</label>
                                <div class="text-muted small ps-5">Video begins playing automatically when in view.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="loop" id="loop" value="1" {{ old('loop', true) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="loop">Loop Playback</label>
                                <div class="text-muted small ps-5">Video repeats seamlessly upon completion.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="muted" id="muted" value="1" {{ old('muted', true) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="muted">Play Muted</label>
                                <div class="text-muted small ps-5">Required by modern browsers for reliable autoplay.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex align-items-center justify-content-between pt-2">
                    <a href="{{ route('admin.showcase.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload &amp; Publish Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewVideoFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const url = URL.createObjectURL(file);
            const player = document.getElementById('videoPreview');
            const container = document.getElementById('videoPreviewContainer');
            player.src = url;
            container.classList.remove('d-none');
            player.load();
        }
    }

    function previewPosterFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('posterPreview');
                const container = document.getElementById('posterPreviewContainer');
                img.src = e.target.result;
                container.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
