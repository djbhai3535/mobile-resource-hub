@extends('layouts.admin')

@section('title', 'Manage Brands')
@section('page_title', 'Mobile Brands')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Mobile Brands Management</h4>
        <p class="text-muted small mb-0">Create, edit, organize, and publish smartphone & hardware manufacturers</p>
    </div>
    @can('create', App\Models\Brand::class)
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Brand
    </a>
    @endcan
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.brands.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search brands by name, slug, or country..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published ({{ $counts['published'] }})</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft ({{ $counts['draft'] }})</option>
                <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived ({{ $counts['archived'] }})</option>
            </select>
        </div>

        <div class="col-md-2">
            <div class="form-check form-switch pt-2">
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="trashedCheck" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="trashedCheck">Trash ({{ $counts['trashed'] }})</label>
            </div>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<!-- Brands Table -->
<div class="mrh-card p-0 overflow-hidden">
    @if($brands->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-award fs-1 d-block mb-2 text-secondary opacity-50"></i>
        <p class="mb-0">No mobile brands found matching your criteria.</p>
    </div>
    @else
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Brand</th>
                    <th>Country</th>
                    <th>Devices</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            @if($brand->logo)
                                <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="rounded border p-1 bg-white" style="width: 40px; height: 40px; object-fit: contain;">
                            @else
                                <div class="rounded border d-flex align-items-center justify-content-center bg-body-tertiary text-muted fw-bold" style="width: 40px; height: 40px;">
                                    {{ substr($brand->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="fw-bold text-decoration-none text-body">
                                    {{ $brand->name }}
                                </a>
                                <div class="small text-muted">/{{ $brand->slug }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $brand->country_of_origin ?: '—' }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.devices.index', ['brand_id' => $brand->id]) }}" class="badge bg-primary-subtle text-primary border text-decoration-none">
                            {{ $brand->devices_count ?? 0 }} devices
                        </a>
                    </td>
                    <td>
                        <span class="small text-muted">{{ $brand->sort_order }}</span>
                    </td>
                    <td>
                        @if($brand->status === 'published')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Published</span>
                        @elseif($brand->status === 'draft')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Draft</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Archived</span>
                        @endif
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1">
                            @if($brand->trashed())
                                @can('restore', $brand)
                                <form action="{{ route('admin.brands.restore', $brand->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Restore Brand">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>
                                @endcan
                            @else
                                <a href="{{ route('public.brand.detail', $brand->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Public Page">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                                @can('update', $brand)
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-outline-primary" title="Edit Brand">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endcan
                                @can('delete', $brand)
                                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" onsubmit="return confirm('Move brand {{ $brand->name }} to trash?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Brand">
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
        {{ $brands->links() }}
    </div>
    @endif
</div>
@endsection
