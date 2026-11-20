@extends('layouts.admin')

@section('title', '3D Mobile Showcase Management')
@section('page_title', '3D Mobile Showcase')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">3D Mobile Showcase Videos</h4>
        <p class="text-muted small mb-0">Manage 2–3 interactive 3D smartphone videos featured below the public navigation bar.</p>
    </div>
    <a href="{{ route('admin.showcase.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
        <i class="bi bi-plus-lg me-1"></i> Upload 3D Showcase Video
    </a>
</div>

<!-- Info Alert / Guidance -->
<div class="alert alert-info border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 mb-4" style="background: rgba(37, 99, 235, 0.08); color: #1d4ed8;">
    <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
    <div class="small">
        <strong>Homepage Display Note:</strong> When 1 or more showcase videos are enabled, an elegant 3D video carousel is featured right below the navigation bar and above the search hero. If all videos are disabled or none exist, the section is completely hidden from the public website.
    </div>
</div>

<div class="mrh-card p-0 overflow-hidden">
    @if($items->isEmpty())
        <div class="p-5 text-center">
            <div class="brand-badge-lumzy mx-auto mb-3" style="width: 64px; height: 64px; font-size: 2rem; background: rgba(37, 99, 235, 0.1); color: #2563EB;">
                <i class="bi bi-badge-3d"></i>
            </div>
            <h5 class="fw-bold mb-1">No 3D Showcase Videos Yet</h5>
            <p class="text-muted small mb-4" style="max-width: 480px; margin: 0 auto;">
                Upload your 3D smartphone showcase videos (MP4 or WebM format) directly from your PC to create a moving 3D presentation on the public homepage.
            </p>
            <a href="{{ route('admin.showcase.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                <i class="bi bi-cloud-arrow-up me-1"></i> Upload First Video
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-body-tertiary">
                    <tr class="text-muted small text-uppercase">
                        <th style="width: 160px;">Video Preview</th>
                        <th>Showcase Details</th>
                        <th>Brand</th>
                        <th>Order</th>
                        <th>Playback</th>
                        <th>Status</th>
                        <th class="text-end pe-4" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>
                            <div class="rounded overflow-hidden border shadow-sm position-relative bg-dark" style="width: 150px; height: 90px;">
                                <video 
                                    src="{{ $item->video_url }}" 
                                    poster="{{ $item->poster_url }}" 
                                    class="w-100 h-100 object-fit-cover" 
                                    muted 
                                    playsinline 
                                    loop 
                                    onmouseover="this.play()" 
                                    onmouseout="this.pause()"
                                ></video>
                                <span class="badge bg-dark-subtle text-white position-absolute bottom-0 start-0 m-1 px-1 py-0 small" style="font-size: 0.65rem;">
                                    {{ strtoupper($item->video_format) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-body mb-0">{{ $item->title }}</div>
                            @if($item->subtitle)
                                <div class="text-muted small text-truncate" style="max-width: 280px;">{{ $item->subtitle }}</div>
                            @endif
                        </td>
                        <td>
                            @if($item->brand_name)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                    {{ $item->brand_name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-body border rounded-pill px-2 py-1 font-monospace">
                                #{{ $item->sort_order }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1" style="font-size: 0.72rem;">
                                @if($item->autoplay)<span class="badge bg-secondary-subtle text-secondary">Autoplay</span>@endif
                                @if($item->loop)<span class="badge bg-secondary-subtle text-secondary">Loop</span>@endif
                                @if($item->muted)<span class="badge bg-secondary-subtle text-secondary">Muted</span>@endif
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('admin.showcase.toggle', $item) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="Toggle visibility">
                                    @if($item->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 rounded-pill">
                                            <i class="bi bi-eye-slash me-1"></i> Hidden
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.showcase.edit', $item) }}" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Edit Video">
                                    <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                </a>
                                <form action="{{ route('admin.showcase.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this showcase video?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Delete Video">
                                        <i class="bi bi-trash3-fill" style="font-size: 0.75rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
