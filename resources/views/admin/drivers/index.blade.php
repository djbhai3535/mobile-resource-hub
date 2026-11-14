@extends('layouts.admin')

@section('title', 'Manage USB Drivers')
@section('page_title', 'USB Drivers & Connectivity')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">USB Drivers Catalog</h4>
        <p class="text-muted small mb-0">Samsung USB, Qualcomm QDLoader 9008, MediaTek VCOM, Google ADB, and Unisoc drivers</p>
    </div>
    <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add USB Driver
    </a>
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.drivers.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search driver name, chipset, OS..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="brand_id" class="form-select">
                <option value="">All Brands / Chipsets</option>
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
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="driverTrashed" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="driverTrashed">Trash ({{ $counts['trashed'] }})</label>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
            @if(request()->anyFilled(['q', 'brand_id', 'status', 'trashed']))
                <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Drivers Table -->
<div class="mrh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 250px;">Driver Package</th>
                    <th>Brand / Ecosystem</th>
                    <th>OS & Architecture</th>
                    <th>Driver Version</th>
                    <th>Size & Mirrors</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $drv)
                <tr>
                    <td>
                        <div class="fw-bold text-body">{{ $drv->name }}</div>
                        <div class="small text-muted font-monospace">{{ $drv->file_type }}</div>
                        @if($drv->is_verified)
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 0.7rem;">
                                <i class="bi bi-patch-check-fill me-1"></i>Digitally Signed & Tested
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($drv->brand)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $drv->brand->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                Universal / Generic
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $drv->windows_versions ?? $drv->os ?? 'Windows All' }}</div>
                        <div class="small text-muted">{{ $drv->architecture ?? '32 & 64-bit' }}</div>
                    </td>
                    <td>
                        <div class="small fw-bold font-monospace">{{ $drv->driver_version ?? 'v1.0' }}</div>
                        <div class="small text-muted">{{ $drv->release_date?->format('M Y') ?? 'Stable' }}</div>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $drv->file_size_formatted ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $drv->mirrors->count() }} mirror(s)</div>
                    </td>
                    <td>
                        @if($drv->trashed())
                            <span class="badge bg-danger">Trashed</span>
                        @elseif($drv->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($drv->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-secondary">Archived</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            @if(!$drv->trashed())
                                <a href="{{ route('admin.drivers.edit', $drv) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($drv->status === 'published')
                                    <a href="{{ route('drivers.show', $drv->slug) }}" target="_blank" class="btn btn-outline-info" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.drivers.destroy', $drv) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this driver package to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Trash">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.drivers.restore', $drv->id) }}" method="POST" class="d-inline">
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
                        <div class="text-muted mb-3"><i class="bi bi-usb-symbol" style="font-size: 3rem;"></i></div>
                        <h6 class="fw-bold">No Drivers Found</h6>
                        <p class="text-muted small">No USB driver packages match your search filters.</p>
                        <a href="{{ route('admin.drivers.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Add First Driver
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $drivers->links() }}
</div>
@endsection
