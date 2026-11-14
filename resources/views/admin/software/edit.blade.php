@extends('layouts.admin')

@section('title', 'Edit Software Tool')
@section('page_title', 'Edit Tool: ' . $software->name)

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Software Tools
    </a>
    @if($software->status === 'published')
        <a href="{{ route('software.show', $software->slug) }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill">
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

<form action="{{ route('admin.software.update', $software) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Identification & Classification -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-tools me-2 text-primary"></i>Tool Identification & Category
                </h5>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold">Software Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $software->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="version" class="form-label fw-semibold">Version</label>
                        <input type="text" name="version" id="version" class="form-control font-monospace @error('version') is-invalid @enderror" value="{{ old('version', $software->version) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug', $software->slug) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="flashing_tools" {{ old('category', $software->category) === 'flashing_tools' ? 'selected' : '' }}>Flashing Tools (Odin, SP Flash, Fastboot)</option>
                            <option value="android_utilities" {{ old('category', $software->category) === 'android_utilities' ? 'selected' : '' }}>Android Utilities (ADB, Fastboot CLI)</option>
                            <option value="mobile_repair_tools" {{ old('category', $software->category) === 'mobile_repair_tools' ? 'selected' : '' }}>Mobile Repair Tools (FRP, IMEI, NVRAM)</option>
                            <option value="backup_tools" {{ old('category', $software->category) === 'backup_tools' ? 'selected' : '' }}>Backup & Restore Tools</option>
                            <option value="recovery_utilities" {{ old('category', $software->category) === 'recovery_utilities' ? 'selected' : '' }}>Custom Recovery Tools (TWRP, OrangeFox)</option>
                            <option value="device_management" {{ old('category', $software->category) === 'device_management' ? 'selected' : '' }}>Device Management & PC Suites</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="developer" class="form-label fw-semibold">Developer / Publisher</label>
                        <input type="text" name="developer" id="developer" class="form-control" value="{{ old('developer', $software->developer) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="supported_os" class="form-label fw-semibold">Supported OS</label>
                        <input type="text" name="supported_os" id="supported_os" class="form-control" value="{{ old('supported_os', $software->supported_os) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="supported_architectures" class="form-label fw-semibold">Architecture</label>
                        <input type="text" name="supported_architectures" id="supported_architectures" class="form-control" value="{{ old('supported_architectures', $software->supported_architectures) }}">
                    </div>
                </div>
            </div>

            <!-- 2. Technical Attributes & Requirements -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-file-earmark-check me-2 text-primary"></i>Technical Package Specs
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="file_format" class="form-label fw-semibold">File Package Format <span class="text-danger">*</span></label>
                        <select name="file_format" id="file_format" class="form-select" required>
                            <option value=".zip" {{ old('file_format', $software->file_format) === '.zip' ? 'selected' : '' }}>.ZIP Archive (Portable)</option>
                            <option value=".exe" {{ old('file_format', $software->file_format) === '.exe' ? 'selected' : '' }}>.EXE Installer / Executable</option>
                            <option value=".msi" {{ old('file_format', $software->file_format) === '.msi' ? 'selected' : '' }}>.MSI Windows Installer</option>
                            <option value=".7z" {{ old('file_format', $software->file_format) === '.7z' ? 'selected' : '' }}>.7Z Compressed Archive</option>
                            <option value=".tar.gz" {{ old('file_format', $software->file_format) === '.tar.gz' ? 'selected' : '' }}>.TAR.GZ Linux Archive</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="file_size_formatted" class="form-label fw-semibold">Display File Size</label>
                        <input type="text" name="file_size_formatted" id="file_size_formatted" class="form-control" value="{{ old('file_size_formatted', $software->file_size_formatted) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="release_date" class="form-label fw-semibold">Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date', $software->release_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="md5_checksum" class="form-label fw-semibold">MD5 Checksum</label>
                        <input type="text" name="md5_checksum" id="md5_checksum" class="form-control font-monospace" value="{{ old('md5_checksum', $software->md5_checksum) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="sha256_checksum" class="form-label fw-semibold">SHA-256 Checksum</label>
                        <input type="text" name="sha256_checksum" id="sha256_checksum" class="form-control font-monospace" value="{{ old('sha256_checksum', $software->sha256_checksum) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="dependencies" class="form-label fw-semibold">Required Dependencies</label>
                        <input type="text" name="dependencies" id="dependencies" class="form-control" value="{{ old('dependencies', $software->dependencies) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="official_website" class="form-label fw-semibold">Developer Official Site</label>
                        <input type="url" name="official_website" id="official_website" class="form-control" value="{{ old('official_website', $software->official_website) }}">
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Tool Description & Features</label>
                        <textarea name="description" id="description" rows="3" class="form-control">{{ old('description', $software->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="installation_instructions" class="form-label fw-semibold">How to Use / Run</label>
                        <textarea name="installation_instructions" id="installation_instructions" rows="3" class="form-control">{{ old('installation_instructions', $software->installation_instructions) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="changelog" class="form-label fw-semibold">Changelog / Release Notes</label>
                        <textarea name="changelog" id="changelog" rows="2" class="form-control">{{ old('changelog', $software->changelog) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Local File Upload & Mirrors -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>Private Storage & Download Mirrors
                </h5>

                @if($software->file_name && $software->storage_path)
                    <div class="alert alert-secondary py-2 small d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="bi bi-file-earmark-lock-fill me-1 text-success"></i>
                            <strong>Current Local Package:</strong> {{ $software->file_name }} ({{ $software->file_size_formatted }})
                            <span class="text-muted font-monospace d-block">Private Path: {{ $software->storage_path }}</span>
                        </div>
                        <span class="badge bg-success">Secured On Disk</span>
                    </div>
                @endif

                <div class="mb-4">
                    <label for="software_file" class="form-label fw-semibold">Upload New Package (Replace Existing)</label>
                    <input type="file" name="software_file" id="software_file" class="form-control @error('software_file') is-invalid @enderror">
                </div>

                <h6 class="fw-bold mb-2">Configured Download Mirrors</h6>
                <div id="mirrorsContainer">
                    @forelse($software->mirrors as $index => $mirror)
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
                                <input type="text" name="mirrors[0][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name" value="Primary Mirror">
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
                        <option value="published" {{ old('status', $software->status) === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status', $software->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ old('status', $software->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" {{ old('is_verified', $software->is_verified) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_verified">Verified Clean & Safe</label>
                </div>

                <div class="mb-3">
                    <div class="small text-muted">Total Download Requests: <strong>{{ $software->download_count }}</strong></div>
                    <div class="small text-muted">Created: {{ $software->created_at?->toFormattedDateString() }}</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Update Software Tool
                    </button>
                    <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary rounded-pill py-2">Cancel</a>
                </div>
            </div>

            <!-- Licensing & Rights -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Licensing & Distribution
                </h5>

                <div class="mb-3">
                    <label for="distribution_permission" class="form-label fw-semibold">Distribution Mode <span class="text-danger">*</span></label>
                    <select name="distribution_permission" id="distribution_permission" class="form-select" required>
                        <option value="authorized_mirror" {{ old('distribution_permission', $software->distribution_permission) === 'authorized_mirror' ? 'selected' : '' }}>Authorized Mirror / CDN Link</option>
                        <option value="site_hosted" {{ old('distribution_permission', $software->distribution_permission) === 'site_hosted' ? 'selected' : '' }}>Site Hosted Mirror</option>
                        <option value="official_link_only" {{ old('distribution_permission', $software->distribution_permission) === 'official_link_only' ? 'selected' : '' }}>Official OEM Link Only</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label for="license_type" class="form-label fw-semibold">License Type <span class="text-danger">*</span></label>
                    <select name="license_type" id="license_type" class="form-select" required>
                        <option value="Freeware" {{ old('license_type', $software->license_type) === 'Freeware' ? 'selected' : '' }}>Freeware (Free for all)</option>
                        <option value="Open Source" {{ old('license_type', $software->license_type) === 'Open Source' ? 'selected' : '' }}>Open Source (GPL / Apache / MIT)</option>
                        <option value="OEM Proprietary" {{ old('license_type', $software->license_type) === 'OEM Proprietary' ? 'selected' : '' }}>OEM Proprietary (Samsung / Xiaomi / MTK)</option>
                        <option value="Trialware" {{ old('license_type', $software->license_type) === 'Trialware' ? 'selected' : '' }}>Trialware / Demo</option>
                    </select>
                </div>
            </div>

            <!-- SEO -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO Settings</h5>
                
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">Meta Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $software->seo_title) }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control">{{ old('meta_description', $software->meta_description) }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url', $software->canonical_url) }}">
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let mirrorIndex = {{ count($software->mirrors) }};
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
