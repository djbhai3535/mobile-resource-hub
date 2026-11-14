<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\FlashFile;
use App\Services\ActivityLogger;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FlashFileController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('flash-files.manage');

        $query = FlashFile::with(['brand', 'device', 'mirrors']);

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('model_number', 'like', $keyword)
                    ->orWhere('chipset', 'like', $keyword)
                    ->orWhere('flashing_tool', 'like', $keyword);
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $flashFiles = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => FlashFile::count(),
            'published' => FlashFile::where('status', 'published')->count(),
            'draft' => FlashFile::where('status', 'draft')->count(),
            'archived' => FlashFile::where('status', 'archived')->count(),
            'trashed' => FlashFile::onlyTrashed()->count(),
        ];

        return view('admin.flash-files.index', compact('flashFiles', 'brands', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('flash-files.manage');

        $brands = Brand::orderBy('name')->get();
        $devices = Device::orderBy('name')->get(['id', 'brand_id', 'name']);
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.flash-files.create', compact('brands', 'devices', 'uploadLimits'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('flash-files.manage');

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:flash_files,slug'],
            'model_number' => ['required', 'string', 'max:100'],
            'chipset' => ['nullable', 'string', 'max:100'],
            'file_type' => ['required', Rule::in(['stock_rom', 'scatter_firmware', 'pac_file', 'qfil_package', 'da_file', 'recovery_image', 'boot_image'])],
            'android_version' => ['nullable', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'flashing_tool' => ['nullable', 'string', 'max:255'],
            'compatibility_notes' => ['nullable', 'string'],
            'installation_instructions' => ['nullable', 'string'],
            'source_website' => ['nullable', 'string', 'max:255'],
            'official_download_url' => ['nullable', 'string', 'max:500'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'payload_file' => ['nullable', 'file', 'max:1048576'],
            // Mirrors
            'mirrors' => ['nullable', 'array'],
            'mirrors.*.mirror_name' => ['nullable', 'string', 'max:100'],
            'mirrors.*.url' => ['nullable', 'string', 'max:1000'],
            'mirrors.*.is_official' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title'] . '-' . ($validated['model_number'] ?: Str::random(5)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('payload_file')) {
            $file = $request->file('payload_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'flash_files');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
        }

        if (!empty($validated['official_download_url']) && !FileStorageService::isSafeExternalUrl($validated['official_download_url'])) {
            return back()->withInput()->withErrors(['official_download_url' => 'Official download URL must be a valid public HTTP or HTTPS address.']);
        }

        $flashFile = DB::transaction(function () use ($validated, $localFilePayload) {
            $flashFile = FlashFile::create($validated);

            if ($localFilePayload) {
                $flashFile->mirrors()->create([
                    'mirror_name' => 'Local Private Storage',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name']) && !empty($m['url'])) {
                        if (FileStorageService::isSafeExternalUrl($m['url'])) {
                            $flashFile->mirrors()->create([
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

            return $flashFile;
        });

        ActivityLogger::log('create', $flashFile, ['title' => $flashFile->title, 'type' => $flashFile->file_type]);

        return redirect()->route('admin.flash-files.index')
            ->with('success', "Flash file '{$flashFile->title}' created successfully.");
    }

    public function edit(FlashFile $flashFile): View
    {
        Gate::authorize('flash-files.manage');

        $flashFile->load(['brand', 'device', 'mirrors']);
        $brands = Brand::orderBy('name')->get();
        $devices = Device::orderBy('name')->get(['id', 'brand_id', 'name']);
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.flash-files.edit', compact('flashFile', 'brands', 'devices', 'uploadLimits'));
    }

    public function update(Request $request, FlashFile $flashFile): RedirectResponse
    {
        Gate::authorize('flash-files.manage');

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'device_id' => ['nullable', 'exists:devices,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('flash_files', 'slug')->ignore($flashFile->id)],
            'model_number' => ['required', 'string', 'max:100'],
            'chipset' => ['nullable', 'string', 'max:100'],
            'file_type' => ['required', Rule::in(['stock_rom', 'scatter_firmware', 'pac_file', 'qfil_package', 'da_file', 'recovery_image', 'boot_image'])],
            'android_version' => ['nullable', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'flashing_tool' => ['nullable', 'string', 'max:255'],
            'compatibility_notes' => ['nullable', 'string'],
            'installation_instructions' => ['nullable', 'string'],
            'source_website' => ['nullable', 'string', 'max:255'],
            'official_download_url' => ['nullable', 'string', 'max:500'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'payload_file' => ['nullable', 'file', 'max:1048576'],
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
            $validated['slug'] = Str::slug($validated['title'] . '-' . ($validated['model_number'] ?: Str::random(5)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('payload_file')) {
            $file = $request->file('payload_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'flash_files');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
        }

        if (!empty($validated['official_download_url']) && !FileStorageService::isSafeExternalUrl($validated['official_download_url'])) {
            return back()->withInput()->withErrors(['official_download_url' => 'Official download URL must be a valid public HTTP or HTTPS address.']);
        }

        DB::transaction(function () use ($flashFile, $validated, $localFilePayload, $request) {
            $flashFile->update($validated);

            if ($localFilePayload) {
                $flashFile->mirrors()->create([
                    'mirror_name' => 'Local Private Storage (' . $localFilePayload['file_name'] . ')',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            if ($request->filled('delete_mirrors')) {
                $flashFile->mirrors()->whereIn('id', $request->delete_mirrors)->delete();
            }

            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name'])) {
                        $url = !empty($m['url']) ? trim($m['url']) : null;
                        if ($url && !FileStorageService::isSafeExternalUrl($url)) {
                            continue;
                        }

                        if (!empty($m['id'])) {
                            $flashFile->mirrors()->where('id', $m['id'])->update([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => $url,
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => isset($m['is_active']) && (bool)$m['is_active'],
                                'sort_order' => $idx + 1,
                            ]);
                        } elseif ($url) {
                            $flashFile->mirrors()->create([
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

        ActivityLogger::log('update', $flashFile, ['title' => $flashFile->title, 'status' => $flashFile->status]);

        return redirect()->route('admin.flash-files.index')
            ->with('success', "Flash file '{$flashFile->title}' updated successfully.");
    }

    public function destroy(FlashFile $flashFile): RedirectResponse
    {
        Gate::authorize('flash-files.manage');

        $title = $flashFile->title;
        $flashFile->delete();

        ActivityLogger::log('delete', $flashFile, ['title' => $title]);

        return redirect()->route('admin.flash-files.index')
            ->with('success', "Flash file '{$title}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $flashFile = FlashFile::onlyTrashed()->findOrFail($id);

        Gate::authorize('flash-files.manage');

        $flashFile->restore();

        ActivityLogger::log('restore', $flashFile, ['title' => $flashFile->title]);

        return redirect()->route('admin.flash-files.index', ['trashed' => 1])
            ->with('success', "Flash file '{$flashFile->title}' restored successfully.");
    }
}
