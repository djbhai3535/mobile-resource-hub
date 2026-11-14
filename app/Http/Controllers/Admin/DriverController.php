<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Driver;
use App\Services\ActivityLogger;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('drivers.manage');

        $query = Driver::with(['brand', 'mirrors']);

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('driver_version', 'like', $keyword)
                    ->orWhere('supported_devices', 'like', $keyword)
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

        $drivers = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => Driver::count(),
            'published' => Driver::where('status', 'published')->count(),
            'draft' => Driver::where('status', 'draft')->count(),
            'archived' => Driver::where('status', 'archived')->count(),
            'trashed' => Driver::onlyTrashed()->count(),
        ];

        return view('admin.drivers.index', compact('drivers', 'brands', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('drivers.manage');

        $brands = Brand::orderBy('name')->get();
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.drivers.create', compact('brands', 'uploadLimits'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('drivers.manage');

        $validated = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:drivers,slug'],
            'driver_version' => ['nullable', 'string', 'max:50'],
            'os' => ['nullable', 'string', 'max:100'],
            'windows_versions' => ['nullable', 'string', 'max:150'],
            'architecture' => ['nullable', 'string', 'max:100'],
            'release_date' => ['nullable', 'date'],
            'supported_devices' => ['nullable', 'string'],
            'file_type' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'official_source' => ['nullable', 'string', 'max:500'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'installation_instructions' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'driver_file' => ['nullable', 'file', 'max:209715'], // Up to 200MB
            // Mirrors
            'mirrors' => ['nullable', 'array'],
            'mirrors.*.mirror_name' => ['nullable', 'string', 'max:100'],
            'mirrors.*.url' => ['nullable', 'string', 'max:1000'],
            'mirrors.*.is_official' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . ($validated['driver_version'] ?? Str::random(4)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'drivers');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
        }

        if (!empty($validated['official_source']) && !FileStorageService::isSafeExternalUrl($validated['official_source'])) {
            return back()->withInput()->withErrors(['official_source' => 'Official source URL must be a valid public HTTP or HTTPS address.']);
        }

        $driver = DB::transaction(function () use ($validated, $localFilePayload) {
            $driver = Driver::create($validated);

            if ($localFilePayload) {
                $driver->mirrors()->create([
                    'mirror_name' => 'Local Private Storage (' . $localFilePayload['file_name'] . ')',
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
                            $driver->mirrors()->create([
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

            return $driver;
        });

        ActivityLogger::log('create', $driver, ['name' => $driver->name, 'version' => $driver->driver_version]);

        return redirect()->route('admin.drivers.index')
            ->with('success', "USB Driver '{$driver->name}' created successfully.");
    }

    public function edit(Driver $driver): View
    {
        Gate::authorize('drivers.manage');

        $driver->load(['brand', 'mirrors']);
        $brands = Brand::orderBy('name')->get();
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.drivers.edit', compact('driver', 'brands', 'uploadLimits'));
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        Gate::authorize('drivers.manage');

        $validated = $request->validate([
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('drivers', 'slug')->ignore($driver->id)],
            'driver_version' => ['nullable', 'string', 'max:50'],
            'os' => ['nullable', 'string', 'max:100'],
            'windows_versions' => ['nullable', 'string', 'max:150'],
            'architecture' => ['nullable', 'string', 'max:100'],
            'release_date' => ['nullable', 'date'],
            'supported_devices' => ['nullable', 'string'],
            'file_type' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'official_source' => ['nullable', 'string', 'max:500'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'installation_instructions' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'driver_file' => ['nullable', 'file', 'max:209715'],
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
            $validated['slug'] = Str::slug($validated['name'] . '-' . ($validated['driver_version'] ?? Str::random(4)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('driver_file')) {
            $file = $request->file('driver_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'drivers');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
        }

        if (!empty($validated['official_source']) && !FileStorageService::isSafeExternalUrl($validated['official_source'])) {
            return back()->withInput()->withErrors(['official_source' => 'Official source URL must be a valid public HTTP or HTTPS address.']);
        }

        DB::transaction(function () use ($driver, $validated, $localFilePayload, $request) {
            $driver->update($validated);

            if ($localFilePayload) {
                $driver->mirrors()->create([
                    'mirror_name' => 'Local Private Storage (' . $localFilePayload['file_name'] . ')',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            if ($request->filled('delete_mirrors')) {
                $driver->mirrors()->whereIn('id', $request->delete_mirrors)->delete();
            }

            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name'])) {
                        $url = !empty($m['url']) ? trim($m['url']) : null;
                        if ($url && !FileStorageService::isSafeExternalUrl($url)) {
                            continue;
                        }

                        if (!empty($m['id'])) {
                            $driver->mirrors()->where('id', $m['id'])->update([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => $url,
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => isset($m['is_active']) && (bool)$m['is_active'],
                                'sort_order' => $idx + 1,
                            ]);
                        } elseif ($url) {
                            $driver->mirrors()->create([
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

        ActivityLogger::log('update', $driver, ['name' => $driver->name, 'status' => $driver->status]);

        return redirect()->route('admin.drivers.index')
            ->with('success', "USB Driver '{$driver->name}' updated successfully.");
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        Gate::authorize('drivers.manage');

        $name = $driver->name;
        $driver->delete();

        ActivityLogger::log('delete', $driver, ['name' => $name]);

        return redirect()->route('admin.drivers.index')
            ->with('success', "USB Driver '{$name}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $driver = Driver::onlyTrashed()->findOrFail($id);

        Gate::authorize('drivers.manage');

        $driver->restore();

        ActivityLogger::log('restore', $driver, ['name' => $driver->name]);

        return redirect()->route('admin.drivers.index', ['trashed' => 1])
            ->with('success', "USB Driver '{$driver->name}' restored successfully.");
    }
}
