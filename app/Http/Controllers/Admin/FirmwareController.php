<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DownloadMirror;
use App\Models\Firmware;
use App\Services\ActivityLogger;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FirmwareController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('firmware.manage');

        $query = Firmware::with(['brand', 'device', 'mirrors']);

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('model_number', 'like', $keyword)
                    ->orWhere('build_number', 'like', $keyword)
                    ->orWhere('csc', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword);
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $firmwares = $query->latest('release_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => Firmware::count(),
            'published' => Firmware::where('status', 'published')->count(),
            'draft' => Firmware::where('status', 'draft')->count(),
            'archived' => Firmware::where('status', 'archived')->count(),
            'trashed' => Firmware::onlyTrashed()->count(),
        ];

        return view('admin.firmware.index', compact('firmwares', 'brands', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('firmware.manage');

        $brands = Brand::orderBy('name')->get();
        $devices = Device::orderBy('name')->get(['id', 'brand_id', 'name']);
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.firmware.create', compact('brands', 'devices', 'uploadLimits'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('firmware.manage');

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:firmwares,slug'],
            'model_number' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'csc' => ['nullable', 'string', 'max:50'],
            'android_version' => ['nullable', 'string', 'max:50'],
            'os_version' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:100'],
            'build_number' => ['nullable', 'string', 'max:100'],
            'security_patch_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
            'file_type' => ['required', 'string', 'max:100'],
            'archive_format' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'flashing_instructions' => ['nullable', 'string'],
            'required_tools' => ['nullable', 'string', 'max:255'],
            'prerequisites' => ['nullable', 'string'],
            'warnings' => ['nullable', 'string'],
            'known_issues' => ['nullable', 'string'],
            'source_website' => ['nullable', 'string', 'max:255'],
            'official_download_url' => ['nullable', 'string', 'max:500'],
            'license_type' => ['nullable', 'string', 'max:100'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'permission_notes' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'firmware_file' => ['nullable', 'file', 'max:1048576'], // Up to 1GB if permitted by server
            // Mirrors
            'mirrors' => ['nullable', 'array'],
            'mirrors.*.mirror_name' => ['nullable', 'string', 'max:100'],
            'mirrors.*.url' => ['nullable', 'string', 'max:1000'],
            'mirrors.*.is_official' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'] . '-' . ($validated['build_number'] ?? Str::random(5)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        // Handle uploaded local file if provided
        $localFilePayload = null;
        if ($request->hasFile('firmware_file')) {
            $file = $request->file('firmware_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'firmware');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
            $validated['archive_format'] = $localFilePayload['extension'];
        }

        // Validate official download URL safety
        if (!empty($validated['official_download_url']) && !FileStorageService::isSafeExternalUrl($validated['official_download_url'])) {
            return back()->withInput()->withErrors(['official_download_url' => 'Official URL must be a valid public HTTP or HTTPS address. Local, private, or unsafe IP addresses are rejected.']);
        }

        $firmware = DB::transaction(function () use ($validated, $localFilePayload, $request) {
            $firmware = Firmware::create($validated);

            // If a local file was uploaded, create the Primary Local Storage mirror
            if ($localFilePayload) {
                $firmware->mirrors()->create([
                    'mirror_name' => 'Primary Local Server Storage',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            // Sync external mirrors
            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name']) && !empty($m['url'])) {
                        if (FileStorageService::isSafeExternalUrl($m['url'])) {
                            $firmware->mirrors()->create([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => trim($m['url']),
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => true,
                                'sort_order' => $idx + 2,
                            ]);
                        }
                    }
                }
            }

            return $firmware;
        });

        ActivityLogger::log('create', $firmware, ['title' => $firmware->title, 'model' => $firmware->model_number]);

        return redirect()->route('admin.firmware.index')
            ->with('success', "Firmware release '{$firmware->title}' created successfully.");
    }

    public function edit(Firmware $firmware): View
    {
        Gate::authorize('firmware.manage');

        $firmware->load(['brand', 'device', 'mirrors']);
        $brands = Brand::orderBy('name')->get();
        $devices = Device::orderBy('name')->get(['id', 'brand_id', 'name']);
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.firmware.edit', compact('firmware', 'brands', 'devices', 'uploadLimits'));
    }

    public function update(Request $request, Firmware $firmware): RedirectResponse
    {
        Gate::authorize('firmware.manage');

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('firmwares', 'slug')->ignore($firmware->id)],
            'model_number' => ['required', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'csc' => ['nullable', 'string', 'max:50'],
            'android_version' => ['nullable', 'string', 'max:50'],
            'os_version' => ['nullable', 'string', 'max:100'],
            'firmware_version' => ['nullable', 'string', 'max:100'],
            'build_number' => ['nullable', 'string', 'max:100'],
            'security_patch_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
            'file_type' => ['required', 'string', 'max:100'],
            'archive_format' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'flashing_instructions' => ['nullable', 'string'],
            'required_tools' => ['nullable', 'string', 'max:255'],
            'prerequisites' => ['nullable', 'string'],
            'warnings' => ['nullable', 'string'],
            'known_issues' => ['nullable', 'string'],
            'source_website' => ['nullable', 'string', 'max:255'],
            'official_download_url' => ['nullable', 'string', 'max:500'],
            'license_type' => ['nullable', 'string', 'max:100'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'permission_notes' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'firmware_file' => ['nullable', 'file', 'max:1048576'],
            // Mirrors
            'mirrors' => ['nullable', 'array'],
            'mirrors.*.id' => ['nullable', 'integer'],
            'mirrors.*.mirror_name' => ['nullable', 'string', 'max:100'],
            'mirrors.*.url' => ['nullable', 'string', 'max:1000'],
            'mirrors.*.is_official' => ['nullable', 'boolean'],
            'mirrors.*.is_active' => ['nullable', 'boolean'],
            'delete_mirrors' => ['nullable', 'array'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'] . '-' . ($validated['build_number'] ?? Str::random(5)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        // Handle uploaded local file if provided
        $localFilePayload = null;
        if ($request->hasFile('firmware_file')) {
            $file = $request->file('firmware_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'firmware');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
            $validated['archive_format'] = $localFilePayload['extension'];
        }

        if (!empty($validated['official_download_url']) && !FileStorageService::isSafeExternalUrl($validated['official_download_url'])) {
            return back()->withInput()->withErrors(['official_download_url' => 'Official URL must be a valid public HTTP or HTTPS address.']);
        }

        DB::transaction(function () use ($firmware, $validated, $localFilePayload, $request) {
            $firmware->update($validated);

            if ($localFilePayload) {
                $firmware->mirrors()->create([
                    'mirror_name' => 'Primary Local Server Storage (' . $localFilePayload['file_name'] . ')',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            // Delete removed mirrors
            if ($request->filled('delete_mirrors')) {
                $firmware->mirrors()->whereIn('id', $request->delete_mirrors)->delete();
            }

            // Sync mirrors
            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name'])) {
                        $url = !empty($m['url']) ? trim($m['url']) : null;
                        if ($url && !FileStorageService::isSafeExternalUrl($url)) {
                            continue;
                        }

                        if (!empty($m['id'])) {
                            $firmware->mirrors()->where('id', $m['id'])->update([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => $url,
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => isset($m['is_active']) && (bool)$m['is_active'],
                                'sort_order' => $idx + 1,
                            ]);
                        } elseif ($url) {
                            $firmware->mirrors()->create([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => $url,
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => true,
                                'sort_order' => $idx + 1,
                            ]);
                        }
                    }
                }
            }
        });

        ActivityLogger::log('update', $firmware, ['title' => $firmware->title, 'status' => $firmware->status]);

        return redirect()->route('admin.firmware.index')
            ->with('success', "Firmware release '{$firmware->title}' updated successfully.");
    }

    public function destroy(Firmware $firmware): RedirectResponse
    {
        Gate::authorize('firmware.manage');

        $title = $firmware->title;
        $firmware->delete();

        ActivityLogger::log('delete', $firmware, ['title' => $title]);

        return redirect()->route('admin.firmware.index')
            ->with('success', "Firmware '{$title}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $firmware = Firmware::onlyTrashed()->findOrFail($id);

        Gate::authorize('firmware.manage');

        $firmware->restore();

        ActivityLogger::log('restore', $firmware, ['title' => $firmware->title]);

        return redirect()->route('admin.firmware.index', ['trashed' => 1])
            ->with('success', "Firmware '{$firmware->title}' restored successfully.");
    }
}
