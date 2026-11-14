@extends('layouts.admin')

@section('title', 'Advertisements')
@section('page_title', 'Advertisement Campaigns & Placements')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Advertisement Campaigns & Monetization</h4>
        <p class="text-muted small mb-0">Manage sponsored creatives, placements, rotation schedules, and performance tracking.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.advertisements.analytics') }}" class="btn btn-outline-info btn-sm">
            <i class="bi bi-graph-up me-1"></i> Campaign Analytics
        </a>
        <a href="{{ route('admin.advertisements.placements') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-layout-text-window me-1"></i> Placements Guide
        </a>
        <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Advertisement
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<!-- Filters -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.advertisements.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <select name="placement" class="form-select form-select-sm">
                <option value="">All Placements</option>
                @foreach($placements as $key => $p)
                    <option value="{{ $key }}" {{ $placementFilter === $key ? 'selected' : '' }}>{{ $p['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Campaigns</option>
                <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Disabled Campaigns</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="mrh-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Creative / Name</th>
                    <th>Placement</th>
                    <th>Type</th>
                    <th class="text-center">Impressions</th>
                    <th class="text-center">Clicks</th>
                    <th class="text-center">CTR</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($advertisements as $ad)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($ad->image_path)
                                    <img src="{{ asset('storage/' . $ad->image_path) }}" alt="" class="rounded border" style="width: 48px; height: 36px; object-fit: cover;">
                                @else
                                    <div class="brand-icon-box" style="width: 36px; height: 36px; font-size: 1rem;">
                                        <i class="bi bi-badge-ad"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $ad->name }}</div>
                                    @if($ad->campaign_name)
                                        <small class="text-muted">{{ $ad->campaign_name }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge text-bg-light border">{{ $placements[$ad->placement]['label'] ?? $ad->placement }}</span>
                        </td>
                        <td>
                            <span class="badge text-bg-secondary text-uppercase" style="font-size: 0.72rem;">{{ $ad->type }}</span>
                        </td>
                        <td class="text-center">{{ number_format($ad->impressions_count) }}</td>
                        <td class="text-center">{{ number_format($ad->clicks_count) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $ad->ctr > 0 ? 'text-bg-success' : 'text-bg-light border' }}">{{ $ad->ctr }}%</span>
                        </td>
                        <td class="text-center">
                            @if($ad->is_active)
                                <span class="badge text-bg-success">Active</span>
                            @else
                                <span class="badge text-bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('admin.advertisements.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.advertisements.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Delete this advertisement campaign?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-badge-ad fs-2 d-block mb-2"></i>
                            No advertisement campaigns created yet. Click "New Advertisement" to begin monetizing.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($advertisements->hasPages())
    <div class="p-3 border-top">
        {{ $advertisements->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
