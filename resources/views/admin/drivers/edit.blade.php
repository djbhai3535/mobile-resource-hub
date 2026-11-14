@extends('layouts.admin')

@section('title', 'Edit USB Driver')
@section('page_title', 'Edit Driver: ' . $driver->name)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Drivers
    </a>
    @if($driver->status === 'published')
        <a href="{{ route('drivers.show', $driver->slug) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill">
            <i class="bi bi-box-arrow-up-right me-1"></i> View Public Page
        </a>
    @endif
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Validation Error!</strong> Please review the form fields below:
        <ul class="mb-0 mt-2 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.drivers.update', $driver) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Identification & Ecosystem -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-usb-symbol me-2 text-primary"></i>Driver Package & Hardware Family
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Driver Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $driver->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="driver_version" class="form-label fw-semibold">Driver Version</label>
                        <input type="text" name="driver_version" id="driver_version" class="form-control font-monospace @error('driver_version') is-invalid @enderror" value="{{ old('driver_version', $driver->driver_version) }}">
                    </div>

                    <div class="col-md-3">
                        <label for="brand_id" class="form-label fw-semibold">Brand / Manufacturer</label>
                        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">-- Universal / Generic --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $driver->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug', $driver->slug) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="file_type" class="form-label fw-semibold">Package Format <span class="text-danger">*</span></label>
                        <select name="file_type" id="file_type" class="form-select" required>
                            <option value="EXE Installer" {{ old('file_type', $driver->file_type) === 'EXE Installer' ? 'selected' : '' }}>EXE Installer (.exe)</option>
                            <option value="INF Driver Archive" {{ old('file_type', $driver->file_type) === 'INF Driver Archive' ? 'selected' : '' }}>INF Archive (.zip / .inf manual install)</option>
                            <option value="MSI Package" {{ old('file_type', $driver->file_type) === 'MSI Package' ? 'selected' : '' }}>MSI Windows Package (.msi)</option>
                            <option value="ZIP Portable" {{ old('file_type', $driver->file_type) === 'ZIP Portable' ? 'selected' : '' }}>ZIP Archive (.zip)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="windows_versions" class="form-label fw-semibold">Supported Windows OS</label>
                        <input type="text" name="windows_versions" id="windows_versions" class="form-control" value="{{ old('windows_versions', $driver->windows_versions) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="architecture" class="form-label fw-semibold">Hardware Architecture</label>
                        <input type="text" name="architecture" id="architecture" class="form-control" value="{{ old('architecture', $driver->architecture) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="release_date" class="form-label fw-semibold">Official Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date', $driver->release_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12">
                        <label for="supported_devices" class="form-label fw-semibold">Supported Device Chipsets & Models</label>
                        <input type="text" name="supported_devices" id="supported_devices" class="form-control" value="{{ old('supported_devices', $driver->supported_devices) }}">
                    </div>
                </div>
            </div>

            <!-- 2. Technical Attributes & Instructions -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>Technical Specs & Checksums
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="file_size_formatted" class="form-label fw-semibold">Display File Size</label>
                        <input type="text" name="file_size_formatted" id="file_size_formatted" class="form-control" value="{{ old('file_size_formatted', $driver->file_size_formatted) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="md5_checksum" class="form-label fw-semibold">MD5 Checksum</label>
                        <input type="text" name="md5_checksum" id="md5_checksum" class="form-control font-monospace" value="{{ old('md5_checksum', $driver->md5_checksum) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="sha256_checksum" class="form-label fw-semibold">SHA-256 Checksum</label>
                        <input type="text" name="sha256_checksum" id="sha256_checksum" class="form-control font-monospace" value="{{ old('sha256_checksum', $driver->sha256_checksum) }}">
                    </div>

                    <div class="col-12">
                        <label for="installation_instructions" class="form-label fw-semibold">Installation Instructions</label>
                        <textarea name="installation_instructions" id="installation_instructions" rows="4" class="form-control">{{ old('installation_instructions', $driver->installation_instructions) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Local File Upload & Mirrors -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>Private Storage & Download Mirrors
                </h5>

                @if($driver->file_name && $driver->storage_path)
                    <div class="alert alert-secondary py-2 small d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-file-earmark-lock-fill me-1 text-success"></i>
                            <strong>Current Local Package:</strong> {{ $driver->file_name }} ({{ $driver->file_size_formatted }})
                            <span class="text-muted font-monospace d-block">Private Path: {{ $driver->storage_path }}</span>
                        </div>
                        <span class="badge bg-success">Stored Privately</span>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="driver_file" class="form-label fw-semibold">Upload New Package (Replace Existing)</label>
                    <input type="file" name="driver_file" id="driver_file" class="form-control @error('driver_file') is-invalid @enderror">
                </div>

                <h6 class="fw-bold mb-2">Configured Download Mirrors</h6>
                <div id="mirrorsContainer">
                    @forelse($driver->mirrors as $index => $mirror)
                        <div class="row g-2 align-items-center mb-2 mirror-row">
                            <input type="hidden" name="mirrors[{{ $index }}][id]" value="{{ $mirror->id }}">
                            <div class="col-md-4">
                                <input type="text" name="mirrors[{{ $index }}][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="{{ old("mirrors.{$index}.mirror_name", $mirror->mirror_name) }}">
                            </div>
                            <div class="col-md-7">
                                <input type="url" name="mirrors[{{ $index }}][url]" class="form-control form-control-sm font-monospace" placeholder="https://..." value="{{ old("mirrors.{$index}.url", $mirror->url) }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mirror-btn"><i class="bi bi-dash"></i></button>
                            </div>
                        </div>
                    @empty
                        <div class="row g-2 align-items-center mb-2 mirror-row">
                            <div class="col-md-4">
                                <input type="text" name="mirrors[0][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="Primary Server">
                            </div>
                            <div class="col-md-7">
                                <input type="url" name="mirrors[0][url]" class="form-control form-control-sm font-monospace" placeholder="https://...">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mirror-btn" disabled><i class="bi bi-dash"></i></button>
                            </div>
                        </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-2" id="addMirrorBtn">
                    <i class="bi bi-plus me-1"></i> Add Another Mirror
                </button>
            </div>
        </div>

        <!-- Sidebar Column -->
        <div class="col-lg-4">
            <!-- Publishing & Status -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Publishing Status</h5>
                
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', $driver->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status', $driver->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ old('status', $driver->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" {{ old('is_verified', $driver->is_verified) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_verified">Digitally Signed & Tested</label>
                </div>

                <div class="mb-3">
                    <div class="small text-muted">Total Download Requests: <strong>{{ $driver->download_count }}</strong></div>
                    <div class="small text-muted">Created: {{ $driver->created_at?->toFormattedDateString() }}</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Update USB Driver
                    </button>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary rounded-pill py-2">Cancel</a>
                </div>
            </div>

            <!-- Distribution & Attribution -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Source & Licensing
                </h5>

                <div class="mb-3">
                    <label for="distribution_permission" class="form-label fw-semibold">Distribution Mode <span class="text-danger">*</span></label>
                    <select name="distribution_permission" id="distribution_permission" class="form-select" required>
                        <option value="site_hosted" {{ old('distribution_permission', $driver->distribution_permission) === 'site_hosted' ? 'selected' : '' }}>Site Hosted Mirror (Safe)</option>
                        <option value="authorized_mirror" {{ old('distribution_permission', $driver->distribution_permission) === 'authorized_mirror' ? 'selected' : '' }}>Authorized Mirror / CDN Link</option>
                        <option value="official_link_only" {{ old('distribution_permission', $driver->distribution_permission) === 'official_link_only' ? 'selected' : '' }}>Official OEM Link Only</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label for="official_source" class="form-label fw-semibold">Official Source Link</label>
                    <input type="url" name="official_source" id="official_source" class="form-control" value="{{ old('official_source', $driver->official_source) }}">
                </div>
            </div>

            <!-- SEO -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO Settings</h5>
                
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">Meta Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $driver->seo_title) }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control">{{ old('meta_description', $driver->meta_description) }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url', $driver->canonical_url) }}">
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let mirrorIndex = {{ count($driver->mirrors) }};
    const container = document.getElementById('mirrorsContainer');
    const addBtn = document.getElementById('addMirrorBtn');

    addBtn.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-center mb-2 mirror-row';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="mirrors[${mirrorIndex}][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="Mirror ${mirrorIndex + 1}">
            </div>
            <div class="col-md-7">
                <input type="url" name="mirrors[${mirrorIndex}][url]" class="form-control form-control-sm font-monospace" placeholder="https://...">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-mirror-btn"><i class="bi bi-dash"></i></button>
            </div>
        `;
        container.appendChild(row);
        mirrorIndex++;
    });

    container.addEventListener('click', function (e) {
        if (e.target.closest('.remove-mirror-btn')) {
            const row = e.target.closest('.mirror-row');
            if (row) {
                row.remove();
            }
        }
    });
});
</script>
@endpush
@endsection
