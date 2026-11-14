@extends('layouts.admin')

@section('title', 'Manage Mobile Devices')
@section('page_title', 'Mobile Devices & Specifications')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Mobile Devices Catalog</h4>
        <p class="text-muted small mb-0">Manage smartphone specs, hardware variants, model numbers, and technical parameters</p>
    </div>
    @can('create', App\Models\Device::class)
    <a href="{{ route('admin.devices.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Device
    </a>
    @endcan
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.devices.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search device or model #..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-2">
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
                <option value="">Pub Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published ({{ $counts['published'] }})</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft ({{ $counts['draft'] }})</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived ({{ $counts['archived'] }})</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="market_status" class="form-select">
                <option value="">Market Status</option>
                <option value="available" {{ request('market_status') === 'available' ? 'selected' : '' }}>Available</option>
                <option value="upcoming" {{ request('market_status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="discontinued" {{ request('market_status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
            </select>
        </div>

        <div class="col-md-1">
            <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="deviceTrashed" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="deviceTrashed">Trash</label>
            </div>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<!-- Devices Table -->
<div class="mrh-card p-0 overflow-hidden">
    @if($devices->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-phone fs-1 d-block mb-2 text-secondary opacity-50"></i>
        <p class="mb-0">No mobile devices found matching your criteria.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Device</th>
                    <th>Brand</th>
                    <th>Category & OS</th>
                    <th>Market Status</th>
                    <th>Pub Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($device->featured_image)
                                <img src="{{ asset('storage/' . $device->featured_image) }}" alt="{{ $device->name }}" class="rounded border p-1 bg-white" style="width: 44px; height: 44px; object-fit: contain;">
                            @else
                                <div class="rounded border d-flex align-items-center justify-content-center bg-body-tertiary text-muted" style="width: 44px; height: 44px;">
                                    <i class="bi bi-phone"></i>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('admin.devices.edit', $device) }}" class="fw-bold text-decoration-none text-body">
                                    {{ $device->name }}
                                </a>
                                @if($device->is_featured)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">Featured</span>
                                @endif
                                <div class="small text-muted font-monospace">
                                    {{ $device->modelNumbers->pluck('model_number')->take(3)->implode(', ') }}
                                    @if($device->modelNumbers->count() > 3) +{{ $device->modelNumbers->count() - 3 }} @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border">{{ $device->brand->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="small text-body fw-semibold text-capitalize">{{ $device->device_category }}</div>
                        <div class="small text-muted">{{ $device->os }} {{ $device->os_version }}</div>
                    </td>
                    <td>
                        @if($device->market_status === 'available')
                            <span class="badge bg-info-subtle text-info border border-info-subtle text-capitalize">Available</span>
                        @elseif($device->market_status === 'upcoming')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-capitalize">Upcoming</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border text-capitalize">Discontinued</span>
                        @endif
                    </td>
                    <td>
                        @if($device->status === 'published')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                        @elseif($device->status === 'draft')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Archived</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1">
                            @if($device->trashed())
                                @can('restore', $device)
                                <form action="{{ route('admin.devices.restore', $device->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Device">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                @endcan
                            @else
                                <a href="{{ route('public.device.detail', $device->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Public Spec Page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @can('update', $device)
                                <a href="{{ route('admin.devices.edit', $device) }}" class="btn btn-sm btn-outline-primary" title="Edit Device">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $device)
                                <form action="{{ route('admin.devices.destroy', $device) }}" method="POST" onsubmit="return confirm('Move device {{ $device->name }} to trash?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Device">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top">
        {{ $devices->links() }}
    </div>
    @endif
</div>
@endsection
