@extends('layouts.admin')

@section('title', 'Media Library')
@section('page_title', 'Image Media Library')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Image Media Library</h4>
        <p class="text-muted small mb-0">Upload, optimize, and manage media assets for devices, articles, and ROM releases</p>
    </div>
</div>

<!-- Upload Card -->
<div class="mrh-card p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up me-1"></i> Upload New Image</h5>

    <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-end">
        @csrf

        <div class="col-md-5">
            <label class="form-label small fw-bold" for="mediaFile">Select Image (JPEG, PNG, WebP, GIF, SVG &bull; Max 5MB)</label>
            <input type="file" name="file" id="mediaFile" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" required>
        </div>

        <div class="col-md-4">
            <label class="form-label small fw-bold" for="alt_text">Alternative Text (Alt Description)</label>
            <input type="text" name="alt_text" id="alt_text" class="form-control" placeholder="e.g. Samsung Galaxy S24 Ultra Titanium Back">
        </div>

        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                <i class="bi bi-upload me-1"></i> Upload Image
            </button>
        </div>
    </form>
</div>

<!-- Search & Filter Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.media.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search by image name or alt text..." value="{{ request('q') }}">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Search</button>
        </div>
    </form>
</div>

<!-- Media Grid -->
@if($media->isEmpty())
<div class="mrh-card p-5 text-center text-muted">
    <i class="bi bi-images fs-1 d-block mb-2"></i>
    <h5>No media items found</h5>
    <p class="small mb-0">Upload your first image asset above.</p>
</div>
@else
<div class="row g-4">
    @foreach($media as $item)
    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
        <div class="mrh-card p-2 h-100 d-flex flex-column">
            <div class="ratio ratio-1x1 bg-body-tertiary rounded-2 overflow-hidden mb-2 border position-relative">
                <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-100 h-100 object-fit-cover" loading="lazy">
            </div>

            <div class="p-1 flex-grow-1">
                <div class="small fw-bold text-truncate text-body" title="{{ $item->name }}">{{ $item->name }}</div>
                <div class="text-muted font-monospace" style="font-size: 0.7rem;">
                    {{ $item->formatted_size }} &bull; {{ strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION)) }}
                </div>
            </div>

            <div class="d-flex gap-1 pt-2 border-top mt-auto">
                <button type="button" class="btn btn-sm btn-outline-secondary flex-grow-1 py-0 px-1 copy-url-btn" data-url="{{ $item->url }}" title="Copy URL">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editMedia_{{ $item->id }}" title="Edit Details">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Permanently delete image?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Media Modal -->
    <div class="modal fade" id="editMedia_{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content mrh-card border-0">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Media Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.media.update', $item) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="img-fluid rounded border" style="max-height: 180px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Alternative Text (Alt)</label>
                            <input type="text" name="alt_text" class="form-control" value="{{ $item->alt_text }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Storage Path / URL</label>
                            <input type="text" class="form-control font-monospace small bg-body-tertiary" value="{{ $item->url }}" readonly>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($media->hasPages())
<div class="p-4 d-flex justify-content-center">
    {{ $media->links() }}
</div>
@endif
@endif

@push('scripts')
<script>
    document.querySelectorAll('.copy-url-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(() => {
                const orig = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2"></i> Copied!';
                setTimeout(() => { this.innerHTML = orig; }, 1800);
            });
        });
    });
</script>
@endpush
@endsection
