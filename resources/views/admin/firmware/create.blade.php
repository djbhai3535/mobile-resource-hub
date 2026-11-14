@extends('layouts.admin')

@section('title', 'Add Firmware Release')
@section('page_title', 'Create Firmware Record')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.firmware.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Firmware List
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

<form action="{{ route('admin.firmware.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- 1. Identification & Device Association -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-phone me-2 text-primary"></i>Device & Package Identification
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label fw-semibold">Mobile Brand <span class="text-danger">*</span></label>
                        <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror" required>
                            <option value="">-- Select Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="device_id" class="form-label fw-semibold">Associated Device</label>
                        <select name="device_id" id="device_id" class="form-select @error('device_id') is-invalid @enderror">
                            <option value="">-- Optional: Link to Device Catalog --</option>
                            @foreach($devices as $device)
                                <option value="{{ $device->id }}" data-brand="{{ $device->brand_id }}" {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                    {{ $device->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('device_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold">Firmware Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. Samsung Galaxy S24 Ultra Android 15 OneUI 7.0 (SM-S928B)" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">URL Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="model_number" class="form-label fw-semibold">Exact Model Number <span class="text-danger">*</span></label>
                        <input type="text" name="model_number" id="model_number" class="form-control font-monospace @error('model_number') is-invalid @enderror" value="{{ old('model_number') }}" placeholder="e.g. SM-S928B, 2312DRA50G" required>
                        @error('model_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="region" class="form-label fw-semibold">Region / Market</label>
                        <input type="text" name="region" id="region" class="form-control @error('region') is-invalid @enderror" value="{{ old('region') }}" placeholder="e.g. Europe, Global, USA Unlocked">
                    </div>

                    <div class="col-md-6">
                        <label for="csc" class="form-label fw-semibold">CSC / Carrier Code</label>
                        <input type="text" name="csc" id="csc" class="form-control font-monospace @error('csc') is-invalid @enderror" value="{{ old('csc') }}" placeholder="e.g. EUX, BTU, INS, TMB">
                    </div>
                </div>
            </div>

            <!-- 2. Technical Version & Build Specifications -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cpu me-2 text-primary"></i>Version & Build Details
                </h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="android_version" class="form-label fw-semibold">Android / Base OS</label>
                        <input type="text" name="android_version" id="android_version" class="form-control @error('android_version') is-invalid @enderror" value="{{ old('android_version') }}" placeholder="e.g. Android 15">
                    </div>

                    <div class="col-md-4">
                        <label for="os_version" class="form-label fw-semibold">Skin / UI Version</label>
                        <input type="text" name="os_version" id="os_version" class="form-control @error('os_version') is-invalid @enderror" value="{{ old('os_version') }}" placeholder="e.g. One UI 7.0, HyperOS 2">
                    </div>

                    <div class="col-md-4">
                        <label for="build_number" class="form-label fw-semibold">Build Number / PDA</label>
                        <input type="text" name="build_number" id="build_number" class="form-control font-monospace @error('build_number') is-invalid @enderror" value="{{ old('build_number') }}" placeholder="e.g. S928BXXU3AXK2">
                    </div>

                    <div class="col-md-4">
                        <label for="firmware_version" class="form-label fw-semibold">Internal Version</label>
                        <input type="text" name="firmware_version" id="firmware_version" class="form-control @error('firmware_version') is-invalid @enderror" value="{{ old('firmware_version') }}" placeholder="e.g. V14.0.5.0">
                    </div>

                    <div class="col-md-4">
                        <label for="security_patch_date" class="form-label fw-semibold">Security Patch Date</label>
                        <input type="date" name="security_patch_date" id="security_patch_date" class="form-control @error('security_patch_date') is-invalid @enderror" value="{{ old('security_patch_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="release_date" class="form-label fw-semibold">Official Release Date</label>
                        <input type="date" name="release_date" id="release_date" class="form-control @error('release_date') is-invalid @enderror" value="{{ old('release_date') }}">
                    </div>

                    <div class="col-md-4">
                        <label for="file_type" class="form-label fw-semibold">File Type <span class="text-danger">*</span></label>
                        <select name="file_type" id="file_type" class="form-select @error('file_type') is-invalid @enderror" required>
                            <option value="Stock ROM" {{ old('file_type', 'Stock ROM') === 'Stock ROM' ? 'selected' : '' }}>Stock ROM (Full 4-File/5-File)</option>
                            <option value="Fastboot ROM" {{ old('file_type') === 'Fastboot ROM' ? 'selected' : '' }}>Fastboot ROM</option>
                            <option value="Recovery ROM" {{ old('file_type') === 'Recovery ROM' ? 'selected' : '' }}>Recovery ROM (OTA / Sideload)</option>
                            <option value="Scatter ROM" {{ old('file_type') === 'Scatter ROM' ? 'selected' : '' }}>Scatter Firmware (MediaTek)</option>
                            <option value="EDL Fastboot" {{ old('file_type') === 'EDL Fastboot' ? 'selected' : '' }}>EDL 9008 Firmware (Qualcomm)</option>
                            <option value="Combination ROM" {{ old('file_type') === 'Combination ROM' ? 'selected' : '' }}>Combination / Engineering ROM</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="archive_format" class="form-label fw-semibold">Archive Format <span class="text-danger">*</span></label>
                        <select name="archive_format" id="archive_format" class="form-select @error('archive_format') is-invalid @enderror" required>
                            <option value=".zip" {{ old('archive_format', '.zip') === '.zip' ? 'selected' : '' }}>.ZIP Archive</option>
                            <option value=".tar.md5" {{ old('archive_format') === '.tar.md5' ? 'selected' : '' }}>.TAR.MD5 (Samsung Odin)</option>
                            <option value=".tgz" {{ old('archive_format') === '.tgz' ? 'selected' : '' }}>.TGZ / .TAR.GZ</option>
                            <option value=".7z" {{ old('archive_format') === '.7z' ? 'selected' : '' }}>.7Z Archive</option>
                            <option value=".rar" {{ old('archive_format') === '.rar' ? 'selected' : '' }}>.RAR Archive</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="file_size_formatted" class="form-label fw-semibold">Display File Size</label>
                        <input type="text" name="file_size_formatted" id="file_size_formatted" class="form-control @error('file_size_formatted') is-invalid @enderror" value="{{ old('file_size_formatted') }}" placeholder="e.g. 7.82 GB">
                    </div>

                    <div class="col-md-6">
                        <label for="md5_checksum" class="form-label fw-semibold">MD5 Checksum</label>
                        <input type="text" name="md5_checksum" id="md5_checksum" class="form-control font-monospace @error('md5_checksum') is-invalid @enderror" value="{{ old('md5_checksum') }}" placeholder="32-character MD5 hash">
                    </div>

                    <div class="col-md-6">
                        <label for="sha256_checksum" class="form-label fw-semibold">SHA-256 Checksum</label>
                        <input type="text" name="sha256_checksum" id="sha256_checksum" class="form-control font-monospace @error('sha256_checksum') is-invalid @enderror" value="{{ old('sha256_checksum') }}" placeholder="64-character SHA-256 hash">
                    </div>
                </div>
            </div>

            <!-- 3. Local File Upload or External Mirrors -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cloud-arrow-down me-2 text-primary"></i>File Storage & Download Mirrors
                </h5>

                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    <strong>Hostinger & Shared Hosting Notice:</strong>
                    Server PHP upload ceiling is <code>{{ $uploadLimits['upload_max_filesize'] }}</code> (POST max: <code>{{ $uploadLimits['post_max_size'] }}</code>).
                    For packages exceeding this limit, provide verified external mirrors below.
                </div>

                <div class="mb-4">
                    <label for="firmware_file" class="form-label fw-semibold">Direct File Upload (Local Private Storage)</label>
                    <input type="file" name="firmware_file" id="firmware_file" class="form-control @error('firmware_file') is-invalid @enderror">
                    <div class="form-text small">Files are stored securely outside the web root (<code>storage/app/private/firmware</code>) and served via chunked streaming.</div>
                    @error('firmware_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <h6 class="fw-bold mb-2">Verified Download Mirrors (Multi-CDN / Multi-Host)</h6>
                <div id="mirrorsContainer">
                    <div class="row g-2 align-items-center mb-2 mirror-row">
                        <div class="col-md-4">
                            <input type="text" name="mirrors[0][mirror_name]" class="form-control form-control-sm" placeholder="Mirror Name (e.g. Official Server, Google Drive, Mega)" value="Official CDN">
                        </div>
                        <div class="col-md-7">
                            <input type="url" name="mirrors[0][url]" class="form-control form-control-sm" placeholder="https://download.example.com/file.zip">
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

            <!-- 4. Flashing Instructions & Technical Notes -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-journal-code me-2 text-primary"></i>Flashing Guide & Safety Warnings
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="required_tools" class="form-label fw-semibold">Required Flashing Tools</label>
                        <input type="text" name="required_tools" id="required_tools" class="form-control" value="{{ old('required_tools') }}" placeholder="e.g. Samsung Odin v3.14.4, SP Flash Tool, Mi Flash">
                    </div>
                    <div class="col-md-6">
                        <label for="prerequisites" class="form-label fw-semibold">Prerequisites</label>
                        <input type="text" name="prerequisites" id="prerequisites" class="form-control" value="{{ old('prerequisites') }}" placeholder="e.g. Charge phone to 50%+, Backup data, Install USB Drivers">
                    </div>
                    <div class="col-12">
                        <label for="flashing_instructions" class="form-label fw-semibold">Step-by-Step Flashing Instructions</label>
                        <textarea name="flashing_instructions" id="flashing_instructions" rows="4" class="form-control" placeholder="1. Extract the firmware archive&#10;2. Open flashing tool as Administrator&#10;3. Put device into Download/Bootloader mode&#10;4. Load BL, AP, CP, CSC slots and click Start">{{ old('flashing_instructions') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="warnings" class="form-label fw-semibold text-danger">Safety Warnings / Data Loss Notices</label>
                        <textarea name="warnings" id="warnings" rows="2" class="form-control" placeholder="Flashing this firmware will erase all internal user data. Do not disconnect the cable during flashing.">{{ old('warnings') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="known_issues" class="form-label fw-semibold">Known Issues / Bug Reports</label>
                        <textarea name="known_issues" id="known_issues" rows="2" class="form-control" placeholder="Optional notes regarding known software bugs or battery drain in this build">{{ old('known_issues') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Options Column -->
        <div class="col-lg-4">
            <!-- Publishing & Status -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Publishing Status</h5>
                
                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Release Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived (Deprecated)</option>
                    </select>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" {{ old('is_verified', 1) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="is_verified">Verified Tested ROM</label>
                    <div class="form-text small">Display the verified test badge on public download pages.</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Save Firmware Release
                    </button>
                    <a href="{{ route('admin.firmware.index') }}" class="btn btn-outline-secondary rounded-pill py-2">Cancel</a>
                </div>
            </div>

            <!-- Legal & Licensing Compliance -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-shield-check me-2 text-primary"></i>Legal & Redistribution
                </h5>

                <div class="mb-3">
                    <label for="distribution_permission" class="form-label fw-semibold">Distribution Mode <span class="text-danger">*</span></label>
                    <select name="distribution_permission" id="distribution_permission" class="form-select" required>
                        <option value="site_hosted" {{ old('distribution_permission') === 'site_hosted' ? 'selected' : '' }}>Site Hosted Mirror (Safe / Verified)</option>
                        <option value="authorized_mirror" {{ old('distribution_permission', 'authorized_mirror') === 'authorized_mirror' ? 'selected' : '' }}>Authorized Mirror / CDN Link</option>
                        <option value="official_link_only" {{ old('distribution_permission') === 'official_link_only' ? 'selected' : '' }}>Official OEM Link Only (Zero Mirroring)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="license_type" class="form-label fw-semibold">License</label>
                    <input type="text" name="license_type" id="license_type" class="form-control" value="{{ old('license_type', 'Proprietary OEM / Free to Flash') }}" placeholder="e.g. Free OEM Firmware">
                </div>

                <div class="mb-3">
                    <label for="official_download_url" class="form-label fw-semibold">Official OEM URL</label>
                    <input type="url" name="official_download_url" id="official_download_url" class="form-control" value="{{ old('official_download_url') }}" placeholder="https://www.samsung.com/...">
                </div>

                <div class="mb-0">
                    <label for="source_website" class="form-label fw-semibold">Source Credits</label>
                    <input type="text" name="source_website" id="source_website" class="form-control" value="{{ old('source_website') }}" placeholder="e.g. Official OTA Server, SamMobile, Xiaomi Community">
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="mrh-card p-4 mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">SEO Metadata</h5>
                
                <div class="mb-3">
                    <label for="seo_title" class="form-label fw-semibold">Meta Title</label>
                    <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title') }}" placeholder="Default: {Title} Download | Mobile Resource Hub">
                </div>

                <div class="mb-3">
                    <label for="meta_description" class="form-label fw-semibold">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" rows="3" class="form-control" placeholder="Search engine description...">{{ old('meta_description') }}</textarea>
                </div>

                <div class="mb-0">
                    <label for="canonical_url" class="form-label fw-semibold">Canonical URL</label>
                    <input type="url" name="canonical_url" id="canonical_url" class="form-control" value="{{ old('canonical_url') }}" placeholder="https://...">
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
                <input type="url" name="mirrors[${mirrorIndex}][url]" class="form-control form-control-sm" placeholder="https://...">
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
