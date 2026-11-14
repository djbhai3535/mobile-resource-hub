@extends('layouts.admin')

@section('title', 'Manage Flash Files')
@section('page_title', 'Flash Files & Repair Firmware')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">Flash Files & Service ROMs</h4>
        <p class="text-muted small mb-0">Scatter files (MediaTek), PAC files (SPD/Unisoc), QFIL XMLs (Qualcomm), DA files, and repair images</p>
    </div>
    <a href="{{ route('admin.flash-files.create') }}" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-plus-lg me-1"></i> Add Flash File
    </a>
</div>

<!-- Filters Bar -->
<div class="mrh-card p-3 mb-4">
    <form action="{{ route('admin.flash-files.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" name="q" class="form-control border-start-0" placeholder="Search title, model #, chipset..." value="{{ request('q') }}">
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
            <select name="file_type" class="form-select">
                <option value="">All File Types</option>
                <option value="scatter_firmware" {{ request('file_type') === 'scatter_firmware' ? 'selected' : '' }}>Scatter (MTK)</option>
                <option value="pac_file" {{ request('file_type') === 'pac_file' ? 'selected' : '' }}>PAC File (SPD)</option>
                <option value="qfil_package" {{ request('file_type') === 'qfil_package' ? 'selected' : '' }}>QFIL XML (Qualcomm)</option>
                <option value="stock_rom" {{ request('file_type') === 'stock_rom' ? 'selected' : '' }}>Stock ROM</option>
                <option value="da_file" {{ request('file_type') === 'da_file' ? 'selected' : '' }}>DA / Auth File</option>
                <option value="recovery_image" {{ request('file_type') === 'recovery_image' ? 'selected' : '' }}>Recovery Image</option>
                <option value="boot_image" {{ request('file_type') === 'boot_image' ? 'selected' : '' }}>Boot Image</option>
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
                <input class="form-check-input" type="checkbox" name="trashed" value="1" id="fileTrashed" {{ request('trashed') ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="form-check-label small" for="fileTrashed">Trash ({{ $counts['trashed'] }})</label>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i></button>
            @if(request()->anyFilled(['q', 'brand_id', 'file_type', 'status', 'trashed']))
                <a href="{{ route('admin.flash-files.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </form>
</div>

<!-- Flash Files Table -->
<div class="mrh-card p-0 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="min-width: 250px;">Package Title</th>
                    <th>Brand & Device</th>
                    <th>Type & Chipset</th>
                    <th>Flashing Tool</th>
                    <th>Size & Mirrors</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($flashFiles as $file)
                <tr>
                    <td>
                        <div class="fw-bold text-body">{{ $file->title }}</div>
                        <div class="small text-muted font-monospace">Model: {{ $file->model_number }}</div>
                        @if($file->is_verified)
                            <span class="badge bg-success-subtle text-success border border-success-subtle mt-1" style="font-size: 0.7rem;">
                                <i class="bi bi-patch-check-fill me-1"></i>Tested & Verified
                            </span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-1">
                            {{ $file->brand->name ?? 'Unknown' }}
                        </span>
                        @if($file->device)
                            <div class="small text-muted">{{ $file->device->name }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle text-uppercase" style="font-size: 0.72rem;">
                            {{ str_replace('_', ' ', $file->file_type) }}
                        </span>
                        @if($file->chipset)
                            <div class="small text-muted font-monospace mt-1">{{ $file->chipset }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $file->flashing_tool ?? 'Generic' }}</div>
                        <div class="small text-muted">{{ $file->android_version ?? 'Any OS' }}</div>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $file->file_size_formatted ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $file->mirrors->count() }} mirror(s)</div>
                    </td>
                    <td>
                        @if($file->trashed())
                            <span class="badge bg-danger">Trashed</span>
                        @elseif($file->status === 'published')
                            <span class="badge bg-success">Published</span>
                        @elseif($file->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-secondary">Archived</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            @if(!$file->trashed())
                                <a href="{{ route('admin.flash-files.edit', $file) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($file->status === 'published')
                                    <a href="{{ route('flash-files.show', $file->slug) }}" target="_blank" class="btn btn-outline-info" title="View Public Page">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                <form action="{{ route('admin.flash-files.destroy', $file) }}" method="POST" class="d-inline" onsubmit="return confirm('Move this flash file to trash?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Trash">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.flash-files.restore', $file->id) }}" method="POST" class="d-inline">
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
                        <div class="text-muted mb-3"><i class="bi bi-file-earmark-zip" style="font-size: 3rem;"></i></div>
                        <h6 class="fw-bold">No Flash Files Found</h6>
                        <p class="text-muted small">No flash files match your search criteria.</p>
                        <a href="{{ route('admin.flash-files.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> Add First Flash File
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center">
    {{ $flashFiles->links() }}
</div>
@endsection
