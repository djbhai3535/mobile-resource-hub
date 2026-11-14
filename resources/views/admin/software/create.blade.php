@extends('layouts.admin')

@section('title', 'Add Software Tool')
@section('page_title', 'Register Software Tool')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.software.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Software Tools
    </a>
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

<form action="{{ route('admin.software.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

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
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Odin Downloader, SP Flash Tool, Mi Flash" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="version" class="form-label fw-semibold">Version</label>
                        <input type="text" name="version" id="version" class="form-control font-monospace @error('version') is-invalid @enderror" value="{{ old('version') }}" placeholder="e.g. v3.14.4, v6.2216">
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control font-monospace @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Auto-generated if empty">
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="flashing_tools" {{ old('category') === 'flashing_tools' ? 'selected' : '' }}>Flashing Tools (Odin, SP Flash, Fastboot)</option>
                            <option value="android_utilities" {{ old('category') === 'android_utilities' ? 'selected' : '' }}>Android Utilities (ADB, Fastboot CLI)</option>
                            <option value="mobile_repair_tools" {{ old('category') === 'mobile_repair_tools' ? 'selected' : '' }}>Mobile Repair Tools (FRP, IMEI, NVRAM)</option>
                            <option value="backup_tools" {{ old('category') === 'backup_tools' ? 'selected' : '' }}>Backup & Restore Tools</option>
                            <option value="recovery_utilities" {{ old('category') === 'recovery_utilities' ? 'selected' : '' }}>Custom Recovery Tools (TWRP, OrangeFox)</option>
                            <option value="device_management" {{ old('category') === 'device_management' ? 'selected' : '' }}>Device Management & PC Suites</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="developer" class="form-label fw-semibold">Developer / Publisher</label>
                        <input type="text" name="developer" id="developer" class="form-control" value="{{ old('developer') }}" placeholder="e.g. Samsung Electronics, MediaTek Inc., Google LLC">
                    </div>

                    <div class="col-md-4">
                        <label for="supported_os" class="form-label fw-semibold">Supported OS</label>
                        <input type="text" name="supported_os" id="supported_os" class="form-control" value="{{ old('supported_os', 'Windows 11, 10, 8.1, 7') }}" placeholder="e.g. Windows 11/10/8/7, macOS, Linux">
                    </div>

                    <div class="col-md-4">
                        <label for="supported_architectures" class="form-label fw-semibold">Architecture</label>
                        <input type="text" name="supported_architectures" id="supported_architectures" class="form-control" value="{{ old('supported_architectures', '32-bit (x86) / 64-bit (x64)') }}">
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
                            <option value=".zip" {{ old('file_format', '.zip') === '.zip' ? 'selected' : '' }}>.ZIP Archive (Portable)</option>
                            <option value=".exe" {{ old('file_format') === '.exe' ? 'selected' : '' }}>.EXE Installer / Executable</option>
                            <option value=".msi" {{ old('file_format') === '.msi' ? 'selected' : '' }}>.MSI Windows Installer</option>
                            <option value=".7z" {{ old('file_format') === '.7z' ? 'selected' : '' }}>.7Z Compressed Archive</option>
                            <option value=".tar.gz" {{ old('file_format') === '.tar.gz' ? 'selected' : '' }}>.TAR.GZ Linux Archive</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="file_size_formatted" class="form-label fw-semibold">Display File Size</label>
                        <input type="text" name="file_size_formatted" id="file_size_formatted" class="form-control" value="{{ old('file_size_formatted') }}" placeholder="e.g. 58.4 MB">
                    </div>

                    <div class="col-md-4">
                        <label for="release_date" class="form-label fw-semibold">Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date') }}">
                    </div>

                    <div class="col-md-6">
                        <label for="md5_checksum" class="form-label fw-semibold">MD5 Checksum</label>
                        <input type="text" name="md5_checksum" id="md5_checksum" class="form-control font-monospace" value="{{ old('md5_checksum') }}" placeholder="MD5 Hash">
                    </div>

                    <div class="col-md-6">
                        <label for="sha256_checksum" class="form-label fw-semibold">SHA-256 Checksum</label>
                        <input type="text" name="sha256_checksum" id="sha256_checksum" class="form-control font-monospace" value="{{ old('sha256_checksum') }}" placeholder="SHA-256 Hash">
                    </div>

                    <div class="col-md-6">
                        <label for="dependencies" class="form-label fw-semibold">Required Dependencies</label>
                        <input type="text" name="dependencies" id="dependencies" class="form-control" value="{{ old('dependencies') }}" placeholder="e.g. Visual C++ 2015 Redistributable, Samsung USB Drivers">
                    </div>

                    <div class="col-md-6">
                        <label for="official_website" class="form-label fw-semibold">Developer Official Site</label>
                        <input type="url" name="official_website" id="official_website" class="form-control" value="{{ old('official_website') }}" placeholder="https://...">
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold">Tool Description & Features</label>
                        <textarea name="description" id="description" rows="3" class="form-control" placeholder="Detailed description of tool capabilities, supported protocols, and use cases...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="installation_instructions" class="form-label fw-semibold">How to Use / Run</label>
                        <textarea name="installation_instructions" id="installation_instructions" rows="3" class="form-control" placeholder="1. Extract zip archive to a local folder&#10;2. Right click executable and select Run as administrator&#10;3. Connect device with USB debugging enabled">{{ old('installation_instructions') }}</textarea>
                    </div>

                    <div class="col-12">
                        <label for="changelog" class="form-label fw-semibold">Changelog / Release Notes</label>
                        <textarea name="changelog" id="changelog" rows="2" class="form-control" placeholder="Version changes, newly supported chipsets, bug fixes...">{{ old('changelog') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 3. Local File Upload & Mirrors -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>Private Storage & Download Mirrors
                </h5>

                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Max upload limit: <code>{{ $uploadLimits['upload_max_filesize'] }}</code>.
                    All software uploads are safely stored in private isolation outside the web directory.
                </div>

                <div class="mb-4">
                    <label for="software_file" class="form-label fw-semibold">Upload Tool Archive / Executable</label>
                    <input type="file" name="software_file" id="software_file" class="form-control @error('software_file') is-invalid @enderror">
                </div>

                <h6 class="fw-bold mb-2">Download Mirrors</h6>
                <div id="mirrorsContainer">
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
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" {{ old('is_verified', 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_verified">Verified Clean & Safe</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Software Tool
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
                        <option value="authorized_mirror" {{ old('distribution_permission', 'authorized_mirror') === 'authorized_mirror' ? 'selected' : '' }}>Authorized Mirror / CDN Link</option>
                        <option value="site_hosted" {{ old('distribution_permission') === 'site_hosted' ? 'selected' : '' }}>Site Hosted Mirror</option>
                        <option value="official_link_only" {{ old('distribution_permission') === 'official_link_only' ? 'selected' : '' }}>Official OEM Link Only</option>
                    </select>
                </div>

                <div class="mb-0">
                    <label for="license_type" class="form-label fw-semibold">License Type <span class="text-danger">*</span></label>
                    <select name="license_type" id="license_type" class="form-select" required>
                        <option value="Freeware" {{ old('license_type', 'Freeware') === 'Freeware' ? 'selected' : '' }}>Freeware (Free for all)</option>
                        <option value="Open Source" {{ old('license_type') === 'Open Source' ? 'selected' : '' }}>Open Source (GPL / Apache / MIT)</option>
                        <option value="OEM Proprietary" {{ old('license_type') === 'OEM Proprietary' ? 'selected' : '' }}>OEM Proprietary (Samsung / Xiaomi / MTK)</option>
                        <option value="Trialware" {{ old('license_type') === 'Trialware' ? 'selected' : '' }}>Trialware / Demo</option>
                    </select>
                </div>
            </div>

            <!-- SEO -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO Settings</h5>
                
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">Meta Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title') }}">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control">{{ old('meta_description') }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url') }}">
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let mirrorIndex = 1;
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
            if (row && container.children.length > 1) {
                row.remove();
            }
        }
    });
});
</script>
@endpush
@endsection
