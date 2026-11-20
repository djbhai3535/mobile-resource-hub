@extends('layouts.admin')

@section('title', 'Edit 3D Mobile Showcase Video')
@section('page_title', 'Edit 3D Showcase Video')

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
                        <h4 class="fw-bold mb-0">Edit 3D Showcase Video</h4>
                        <p class="text-muted small mb-0">{{ $showcase->title }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.showcase.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    &larr; Back to List
                </a>
            </div>

            <form action="{{ route('admin.showcase.update', $showcase) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Section 1: Current Video & Replacement -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-film text-primary"></i> 3D Video File
                    </h5>

                    <!-- Current Video Box -->
                    <div class="card bg-dark border p-3 mb-3 text-white">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div class="rounded overflow-hidden bg-black border border-secondary" style="max-width: 320px; max-height: 180px;">
                                <video 
                                    src="{{ $showcase->video_url }}" 
                                    poster="{{ $showcase->poster_url }}" 
                                    controls 
                                    muted 
                                    class="w-100" 
                                    style="max-height: 180px; object-fit: contain;"
                                ></video>
                            </div>
                            <div class="text-sm-end">
                                <span class="badge bg-secondary mb-1">{{ strtoupper($showcase->video_format) }} Video</span>
                                <div class="small text-white-50 font-monospace text-truncate" style="max-width: 260px;">{{ basename($showcase->video_path) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Replace Input -->
                    <div>
                        <label class="form-label fw-semibold small" for="videoInput">Replace Video File <span class="text-muted font-normal">(Leave blank to keep current)</span></label>
                        <input 
                            type="file" 
                            name="video" 
                            id="videoInput" 
                            class="form-control @error('video') is-invalid @enderror" 
                            accept="video/mp4,video/webm"
                            onchange="previewVideoFile(this)"
                        >
                        <div class="form-text text-muted small">Max size: 50MB (MP4, WebM).</div>
                        @error('video')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="videoPreviewContainer" class="d-none mt-3">
                        <span class="text-muted small fw-semibold d-block mb-1">New Video Selected:</span>
                        <div class="rounded-3 overflow-hidden border shadow-sm bg-dark" style="max-width: 400px; max-height: 220px;">
                            <video id="videoPreview" class="w-100" controls muted playsinline style="max-height: 220px; object-fit: contain;"></video>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Poster Image -->
                <div class="mb-4 pb-4 border-bottom">
                    <h5 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-image text-warning"></i> Video Poster / Thumbnail
                    </h5>

                    @if($showcase->poster_path)
                    <div class="card bg-body-tertiary border p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-1 bg-white rounded border shadow-sm">
                                    <img src="{{ $showcase->poster_url }}" alt="Poster" style="max-width: 140px; max-height: 80px; object-fit: cover;">
                                </div>
                                <div class="small text-muted font-monospace text-truncate" style="max-width: 240px;">
                                    {{ basename($showcase->poster_path) }}
                                </div>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_poster" id="remove_poster" value="1">
                                <label class="form-check-label text-danger small fw-semibold" for="remove_poster">
                                    <i class="bi bi-trash3 me-1"></i> Remove Poster
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div>
                        <label class="form-label fw-semibold small" for="posterInput">Upload New Poster Image</label>
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
                        <span class="text-muted small fw-semibold d-block mb-1">New Poster Selected:</span>
                        <div class="p-1 bg-white rounded border d-inline-block shadow-sm">
                            <img id="posterPreview" src="#" alt="Poster Preview" style="max-width: 200px; max-height: 110px; object-fit: cover;">
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
                            value="{{ old('title', $showcase->title) }}" 
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
                            value="{{ old('subtitle', $showcase->subtitle) }}"
                        >
                        @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold" for="brand_name">Brand Name</label>
                            <input 
                                type="text" 
                                name="brand_name" 
                                id="brand_name" 
                                class="form-control @error('brand_name') is-invalid @enderror" 
                                value="{{ old('brand_name', $showcase->brand_name) }}"
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
                                value="{{ old('sort_order', $showcase->sort_order) }}" 
                                min="0" 
                                max="999"
                            >
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
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="is_active" id="is_active" value="1" {{ old('is_active', $showcase->is_active) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="is_active">Active on Homepage</label>
                                <div class="text-muted small ps-5">Video will appear in public showcase carousel.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="autoplay" id="autoplay" value="1" {{ old('autoplay', $showcase->autoplay) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="autoplay">Autoplay</label>
                                <div class="text-muted small ps-5">Video begins playing automatically when in view.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="loop" id="loop" value="1" {{ old('loop', $showcase->loop) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
                                <label class="form-check-label fw-bold" for="loop">Loop Playback</label>
                                <div class="text-muted small ps-5">Video repeats seamlessly upon completion.</div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-check form-switch p-3 bg-body-tertiary rounded-3 border">
                                <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" name="muted" id="muted" value="1" {{ old('muted', $showcase->muted) ? 'checked' : '' }} style="width: 2.6em; height: 1.3em;">
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
                        <i class="bi bi-check-lg me-1"></i> Save Changes
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
