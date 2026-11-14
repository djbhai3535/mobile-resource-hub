@extends('layouts.admin')

@section('title', 'Manage Software Tools')
@section('page_title', 'Flashing & Mobile Repair Software')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Software Tools & Utilities</h4>
        <p class="text-muted small mb-0">Odin, SP Flash Tool, Mi Flash, Fastboot tools, FRP utilities, and backup software</p>
    </div>
    <a href="{{ route('admin.software.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Tool
    </a>
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.software.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search tool name, developer..." value="{{ request('q') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <option value="flashing_tools" {{ request('category') === 'flashing_tools' ? 'selected' : '' }}>Flashing Tools</option>
                <option value="android_utilities" {{ request('category') === 'android_utilities' ? 'selected' : '' }}>Android Utilities / ADB</option>
                <option value="mobile_repair_tools" {{ request('category') === 'mobile_repair_tools' ? 'selected' : '' }}>Mobile Repair Tools</option>
                <option value="backup_tools" {{ request('category') === 'backup_tools' ? 'selected' : '' }}>Backup & Restore</option>
                <option value="recovery_utilities" {{ request('category') === 'recovery_utilities' ? 'selected' : '' }}>Recovery Utilities</option>
                <option value="device_management" {{ request('category') === 'device_management' ? 'selected' : '' }}>Device Management</option>
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
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="toolTrashed" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="toolTrashed">Trash ({{ $counts['trashed'] }})</label>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
            @if(request()->anyFilled(['q', 'category', 'status', 'trashed']))
                <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Software Table -->
<div class="mrh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 250px;">Software Tool</th>
                    <th>Category</th>
                    <th>OS & Arch</th>
                    <th>License & Developer</th>
                    <th>Size & Mirrors</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($softwareTools as $tool)
                <tr>
                    <td>
                        <div class="fw-bold text-body">{{ $tool->name }}</div>
                        <div class="small text-muted font-monospace">Version: {{ $tool->version ?? 'Latest' }}</div>
                        @if($tool->is_verified)
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 0.7rem;">
                                <i class="bi bi-shield-check me-1"></i>Verified Clean & Tested
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase" style="font-size: 0.72rem;">
                            {{ str_replace('_', ' ', $tool->category) }}
                        </span>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $tool->supported_os ?? 'Windows' }}</div>
                        <div class="small text-muted">{{ $tool->supported_architectures ?? 'x86 / x64' }}</div>
                    </td>
                    <td>
                        <div class="small">{{ $tool->license_type ?? 'Freeware' }}</div>
                        <div class="small text-muted">{{ $tool->developer ?? 'Community' }}</div>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $tool->file_size_formatted ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $tool->mirrors->count() }} mirror(s)</div>
                    </td>
                    <td>
                        @if($tool->trashed())
                            <span class="badge bg-danger">Trashed</span>
                        @elseif($tool->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($tool->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-secondary">Archived</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            @if(!$tool->trashed())
                                <a href="{{ route('admin.software.edit', $tool) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($tool->status === 'published')
                                    <a href="{{ route('software.show', $tool->slug) }}" target="_blank" class="btn btn-outline-info" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.software.destroy', $tool) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this software tool to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Trash">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.software.restore', $tool->id) }}" method="POST" class="d-inline">
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
                        <div class="text-muted mb-3"><i class="bi bi-tools" style="font-size: 3rem;"></i></div>
                        <h6 class="fw-bold">No Software Tools Found</h6>
                        <p class="text-muted small">No flashing utilities or tools match your search.</p>
                        <a href="{{ route('admin.software.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Add First Tool
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $softwareTools->links() }}
</div>
@endsection
