<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SoftwareTool;
use App\Services\ActivityLogger;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SoftwareController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('software.manage');

        $query = SoftwareTool::with('mirrors');

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('developer', 'like', $keyword)
                    ->orWhere('version', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $softwareTools = $query->latest('id')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total' => SoftwareTool::count(),
            'published' => SoftwareTool::where('status', 'published')->count(),
            'draft' => SoftwareTool::where('status', 'draft')->count(),
            'archived' => SoftwareTool::where('status', 'archived')->count(),
            'trashed' => SoftwareTool::onlyTrashed()->count(),
        ];

        return view('admin.software.index', compact('softwareTools', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('software.manage');

        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.software.create', compact('uploadLimits'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('software.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:software_tools,slug'],
            'category' => ['required', Rule::in(['flashing_tools', 'android_utilities', 'mobile_repair_tools', 'backup_tools', 'recovery_utilities', 'device_management'])],
            'version' => ['nullable', 'string', 'max:50'],
            'developer' => ['nullable', 'string', 'max:150'],
            'supported_os' => ['nullable', 'string', 'max:150'],
            'supported_architectures' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'changelog' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'file_format' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'license_type' => ['required', 'string', 'max:100'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'official_website' => ['nullable', 'string', 'max:500'],
            'dependencies' => ['nullable', 'string', 'max:255'],
            'installation_instructions' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'software_file' => ['nullable', 'file', 'max:524288'], // Up to 512MB
            // Mirrors
            'mirrors' => ['nullable', 'array'],
            'mirrors.*.mirror_name' => ['nullable', 'string', 'max:100'],
            'mirrors.*.url' => ['nullable', 'string', 'max:1000'],
            'mirrors.*.is_official' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . ($validated['version'] ?? Str::random(4)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('software_file')) {
            $file = $request->file('software_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'software');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
            $validated['file_format'] = $localFilePayload['extension'];
        }

        if (!empty($validated['official_website']) && !FileStorageService::isSafeExternalUrl($validated['official_website'])) {
            return back()->withInput()->withErrors(['official_website' => 'Official website must be a valid public HTTP or HTTPS address.']);
        }

        $tool = DB::transaction(function () use ($validated, $localFilePayload) {
            $tool = SoftwareTool::create($validated);

            if ($localFilePayload) {
                $tool->mirrors()->create([
                    'mirror_name' => 'Local Private Mirror (' . $localFilePayload['file_name'] . ')',
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
                            $tool->mirrors()->create([
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

            return $tool;
        });

        ActivityLogger::log('create', $tool, ['name' => $tool->name, 'version' => $tool->version]);

        return redirect()->route('admin.software.index')
            ->with('success', "Software tool '{$tool->name}' created successfully.");
    }

    public function edit(SoftwareTool $software): View
    {
        Gate::authorize('software.manage');

        $software->load('mirrors');
        $uploadLimits = FileStorageService::getPhpUploadLimits();

        return view('admin.software.edit', compact('software', 'uploadLimits'));
    }

    public function update(Request $request, SoftwareTool $software): RedirectResponse
    {
        Gate::authorize('software.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('software_tools', 'slug')->ignore($software->id)],
            'category' => ['required', Rule::in(['flashing_tools', 'android_utilities', 'mobile_repair_tools', 'backup_tools', 'recovery_utilities', 'device_management'])],
            'version' => ['nullable', 'string', 'max:50'],
            'developer' => ['nullable', 'string', 'max:150'],
            'supported_os' => ['nullable', 'string', 'max:150'],
            'supported_architectures' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'changelog' => ['nullable', 'string'],
            'release_date' => ['nullable', 'date'],
            'file_format' => ['required', 'string', 'max:50'],
            'file_size_formatted' => ['nullable', 'string', 'max:50'],
            'md5_checksum' => ['nullable', 'string', 'max:64'],
            'sha256_checksum' => ['nullable', 'string', 'max:64'],
            'license_type' => ['required', 'string', 'max:100'],
            'distribution_permission' => ['required', Rule::in(['official_link_only', 'authorized_mirror', 'site_hosted'])],
            'official_website' => ['nullable', 'string', 'max:500'],
            'dependencies' => ['nullable', 'string', 'max:255'],
            'installation_instructions' => ['nullable', 'string'],
            'is_verified' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Local file upload
            'software_file' => ['nullable', 'file', 'max:524288'],
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
            $validated['slug'] = Str::slug($validated['name'] . '-' . ($validated['version'] ?? Str::random(4)));
        }

        $validated['is_verified'] = $request->has('is_verified');

        $localFilePayload = null;
        if ($request->hasFile('software_file')) {
            $file = $request->file('software_file');
            $localFilePayload = FileStorageService::storePrivateFile($file, 'software');

            $validated['file_size_bytes'] = $localFilePayload['size_bytes'];
            $validated['file_size_formatted'] = $localFilePayload['size_formatted'];
            $validated['md5_checksum'] = $localFilePayload['md5'];
            $validated['sha256_checksum'] = $localFilePayload['sha256'];
            $validated['file_format'] = $localFilePayload['extension'];
        }

        if (!empty($validated['official_website']) && !FileStorageService::isSafeExternalUrl($validated['official_website'])) {
            return back()->withInput()->withErrors(['official_website' => 'Official website must be a valid public HTTP or HTTPS address.']);
        }

        DB::transaction(function () use ($software, $validated, $localFilePayload, $request) {
            $software->update($validated);

            if ($localFilePayload) {
                $software->mirrors()->create([
                    'mirror_name' => 'Local Private Mirror (' . $localFilePayload['file_name'] . ')',
                    'file_path' => $localFilePayload['relative_path'],
                    'disk' => $localFilePayload['disk'],
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }

            if ($request->filled('delete_mirrors')) {
                $software->mirrors()->whereIn('id', $request->delete_mirrors)->delete();
            }

            if (!empty($validated['mirrors'])) {
                foreach ($validated['mirrors'] as $idx => $m) {
                    if (!empty($m['mirror_name'])) {
                        $url = !empty($m['url']) ? trim($m['url']) : null;
                        if ($url && !FileStorageService::isSafeExternalUrl($url)) {
                            continue;
                        }

                        if (!empty($m['id'])) {
                            $software->mirrors()->where('id', $m['id'])->update([
                                'mirror_name' => trim($m['mirror_name']),
                                'url' => $url,
                                'is_official' => isset($m['is_official']) && (bool)$m['is_official'],
                                'is_active' => isset($m['is_active']) && (bool)$m['is_active'],
                                'sort_order' => $idx + 1,
                            ]);
                        } elseif ($url) {
                            $software->mirrors()->create([
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

        ActivityLogger::log('update', $software, ['name' => $software->name, 'status' => $software->status]);

        return redirect()->route('admin.software.index')
            ->with('success', "Software tool '{$software->name}' updated successfully.");
    }

    public function destroy(SoftwareTool $software): RedirectResponse
    {
        Gate::authorize('software.manage');

        $name = $software->name;
        $software->delete();

        ActivityLogger::log('delete', $software, ['name' => $name]);

        return redirect()->route('admin.software.index')
            ->with('success', "Software '{$name}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $software = SoftwareTool::onlyTrashed()->findOrFail($id);

        Gate::authorize('software.manage');

        $software->restore();

        ActivityLogger::log('restore', $software, ['name' => $software->name]);

        return redirect()->route('admin.software.index', ['trashed' => 1])
            ->with('success', "Software '{$software->name}' restored successfully.");
    }
}
