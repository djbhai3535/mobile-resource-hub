@extends('layouts.admin')

@section('title', 'Manage Firmware Releases')
@section('page_title', 'Firmware & Stock ROMs')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Firmware Releases</h4>
        <p class="text-muted small mb-0">Official stock ROMs, OS releases, region codes (CSC), and multi-mirror downloads</p>
    </div>
    <a href="{{ route('admin.firmware.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Firmware
    </a>
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.firmware.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search title, model #, build, CSC..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="brand_id" class="form-select">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Status (All)</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published ({{ $counts['published'] }})</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft ({{ $counts['draft'] }})</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived ({{ $counts['archived'] }})</option>
            </select>
        </div>

        <div class="col-md-2">
            <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="firmwareTrashed" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="firmwareTrashed">Trash ({{ $counts['trashed'] }})</label>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
            @if(request()->anyFilled(['q', 'brand_id', 'status', 'trashed']))
                <a href="{{ route('admin.firmware.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Firmware Table -->
<div class="mrh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 250px;">Firmware Release</th>
                    <th>Brand & Device</th>
                    <th>Build / Version</th>
                    <th>Region / CSC</th>
                    <th>Size & Mirrors</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($firmwares as $fw)
                <tr>
                    <td>
                        <div class="fw-bold text-body">{{ $fw->title }}</div>
                        <div class="small text-muted font-monospace">Model: {{ $fw->model_number }}</div>
                        @if($fw->is_verified)
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 0.7rem;">
                                <i class="bi bi-patch-check-fill me-1"></i>Verified Working
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                            {{ $fw->brand->name ?? 'Unknown Brand' }}
                        </span>
                        @if($fw->device)
                            <div class="small text-muted">{{ $fw->device->name }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $fw->android_version ?? 'N/A' }}</div>
                        <div class="small text-muted font-monospace">{{ $fw->build_number ?? 'Build: N/A' }}</div>
                    </td>
                    <td>
                        <div class="small">{{ $fw->region ?? 'Global' }}</div>
                        @if($fw->csc)
                            <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">{{ $fw->csc }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $fw->file_size_formatted ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $fw->mirrors->count() }} active mirror(s)</div>
                    </td>
                    <td>
                        @if($fw->trashed())
                            <span class="badge bg-danger">Trashed</span>
                        @elseif($fw->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($fw->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-secondary">Archived</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            @if(!$fw->trashed())
                                <a href="{{ route('admin.firmware.edit', $fw) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($fw->status === 'published')
                                    <a href="{{ route('firmware.show', $fw->slug) }}" target="_blank" class="btn btn-outline-info" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.firmware.destroy', $fw) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this firmware release to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Trash">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.firmware.restore', $fw->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" title="Restore">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="text-muted mb-3"><i class="bi bi-hdd-network" style="font-size: 3rem;"></i></div>
                        <h6 class="fw-bold">No Firmware Releases Found</h6>
                        <p class="text-muted small">No firmware packages match the current criteria.</p>
                        <a href="{{ route('admin.firmware.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Add First Firmware
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $firmwares->links() }}
</div>
@endsection
