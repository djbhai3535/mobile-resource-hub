<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModelNumber;
use App\Models\DeviceSpecificationValue;
use App\Models\DeviceVariant;
use App\Models\SpecificationGroup;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DeviceController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Device::class);

        $query = Device::with(['brand', 'modelNumbers', 'variants']);

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('marketing_name', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword)
                    ->orWhereHas('modelNumbers', function ($mq) use ($keyword) {
                        $mq->where('model_number', 'like', $keyword);
                    });
            });
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('market_status')) {
            $query->where('market_status', $request->market_status);
        }

        if ($request->filled('device_category')) {
            $query->where('device_category', $request->device_category);
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $devices = $query->latest('release_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $brands = Brand::orderBy('name')->get(['id', 'name']);

        $counts = [
            'total' => Device::count(),
            'published' => Device::where('status', 'published')->count(),
            'draft' => Device::where('status', 'draft')->count(),
            'archived' => Device::where('status', 'archived')->count(),
            'trashed' => Device::onlyTrashed()->count(),
        ];

        return view('admin.devices.index', compact('devices', 'brands', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('create', Device::class);

        $brands = Brand::orderBy('name')->get();
        $specificationGroups = SpecificationGroup::with(['fields' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.devices.create', compact('brands', 'specificationGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Device::class);

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'marketing_name' => ['nullable', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:devices,slug'],
            'product_description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'announced_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'market_status' => ['required', Rule::in(['upcoming', 'available', 'discontinued'])],
            'os' => ['nullable', 'string', 'max:100'],
            'os_version' => ['nullable', 'string', 'max:100'],
            'device_category' => ['required', Rule::in(['smartphone', 'tablet', 'feature_phone', 'smartwatch'])],
            'official_product_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Model numbers
            'model_numbers' => ['nullable', 'array'],
            'model_numbers.*.model_number' => ['nullable', 'string', 'max:100'],
            'model_numbers.*.region' => ['nullable', 'string', 'max:100'],
            'model_numbers.*.is_primary' => ['nullable', 'boolean'],
            // Variants
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['nullable', 'string', 'max:150'],
            'variants.*.ram' => ['nullable', 'string', 'max:50'],
            'variants.*.internal_storage' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:100'],
            'variants.*.notes' => ['nullable', 'string', 'max:255'],
            // Dynamic specs
            'specs' => ['nullable', 'array'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Author publish restriction
        if (!auth()->user()->isEditor() && $validated['status'] === 'published') {
            $validated['status'] = 'draft';
        }

        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $fileName = Str::random(32) . '.' . strtolower($file->getClientOriginalExtension());
            $validated['featured_image'] = $file->storeAs('devices', $fileName, 'public');
        }

        $gallery = [];
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $gFile) {
                $gName = Str::random(32) . '.' . strtolower($gFile->getClientOriginalExtension());
                $gallery[] = $gFile->storeAs('devices/gallery', $gName, 'public');
            }
        }
        $validated['image_gallery'] = !empty($gallery) ? $gallery : null;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $device = DB::transaction(function () use ($validated, $request) {
            $device = Device::create($validated);

            // Sync model numbers
            if (!empty($validated['model_numbers'])) {
                foreach ($validated['model_numbers'] as $m) {
                    if (!empty($m['model_number'])) {
                        DeviceModelNumber::create([
                            'device_id' => $device->id,
                            'model_number' => trim($m['model_number']),
                            'region' => $m['region'] ?? null,
                            'is_primary' => isset($m['is_primary']) && (bool)$m['is_primary'],
                        ]);
                    }
                }
            }

            // Sync variants
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (!empty($v['name'])) {
                        DeviceVariant::create([
                            'device_id' => $device->id,
                            'name' => trim($v['name']),
                            'ram' => $v['ram'] ?? null,
                            'internal_storage' => $v['internal_storage'] ?? null,
                            'color' => $v['color'] ?? null,
                            'notes' => $v['notes'] ?? null,
                        ]);
                    }
                }
            }

            // Sync dynamic specification values
            if (!empty($validated['specs'])) {
                foreach ($validated['specs'] as $fieldId => $specVal) {
                    if ($specVal !== null && trim($specVal) !== '') {
                        DeviceSpecificationValue::create([
                            'device_id' => $device->id,
                            'field_id' => (int) $fieldId,
                            'value' => trim($specVal),
                        ]);
                    }
                }
            }

            return $device;
        });

        ActivityLogger::log('create', $device, ['name' => $device->name, 'status' => $device->status]);

        return redirect()->route('admin.devices.index')
            ->with('success', "Device '{$device->name}' created successfully.");
    }

    public function edit(Device $device): View
    {
        Gate::authorize('update', $device);

        $device->load(['brand', 'modelNumbers', 'variants', 'specificationValues']);
        $brands = Brand::orderBy('name')->get();
        $specificationGroups = SpecificationGroup::with(['fields' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('sort_order')->get();

        $existingSpecValues = $device->specificationValues->pluck('value', 'field_id')->toArray();

        return view('admin.devices.edit', compact('device', 'brands', 'specificationGroups', 'existingSpecValues'));
    }

    public function update(Request $request, Device $device): RedirectResponse
    {
        Gate::authorize('update', $device);

        $validated = $request->validate([
            'brand_id' => ['required', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:150'],
            'marketing_name' => ['nullable', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('devices', 'slug')->ignore($device->id)],
            'product_description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:3072'],
            'remove_gallery_images' => ['nullable', 'array'],
            'announced_date' => ['nullable', 'date'],
            'release_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'market_status' => ['required', Rule::in(['upcoming', 'available', 'discontinued'])],
            'os' => ['nullable', 'string', 'max:100'],
            'os_version' => ['nullable', 'string', 'max:100'],
            'device_category' => ['required', Rule::in(['smartphone', 'tablet', 'feature_phone', 'smartwatch'])],
            'official_product_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            // Model numbers
            'model_numbers' => ['nullable', 'array'],
            'model_numbers.*.model_number' => ['nullable', 'string', 'max:100'],
            'model_numbers.*.region' => ['nullable', 'string', 'max:100'],
            'model_numbers.*.is_primary' => ['nullable', 'boolean'],
            // Variants
            'variants' => ['nullable', 'array'],
            'variants.*.name' => ['nullable', 'string', 'max:150'],
            'variants.*.ram' => ['nullable', 'string', 'max:50'],
            'variants.*.internal_storage' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:100'],
            'variants.*.notes' => ['nullable', 'string', 'max:255'],
            // Dynamic specs
            'specs' => ['nullable', 'array'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Author publish restriction
        if (!auth()->user()->isEditor() && $validated['status'] === 'published') {
            $validated['status'] = 'draft';
        }

        // Featured image handling
        if ($request->boolean('remove_featured_image')) {
            if ($device->featured_image && Storage::disk('public')->exists($device->featured_image)) {
                Storage::disk('public')->delete($device->featured_image);
            }
            $validated['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            if ($device->featured_image && Storage::disk('public')->exists($device->featured_image)) {
                Storage::disk('public')->delete($device->featured_image);
            }
            $file = $request->file('featured_image');
            $fileName = Str::random(32) . '.' . strtolower($file->getClientOriginalExtension());
            $validated['featured_image'] = $file->storeAs('devices', $fileName, 'public');
        } else {
            unset($validated['featured_image']);
        }
        unset($validated['remove_featured_image']);

        // Gallery images handling
        $currentGallery = $device->image_gallery ?? [];
        if ($request->filled('remove_gallery_images')) {
            $toRemove = $request->remove_gallery_images;
            foreach ($toRemove as $rem) {
                if (Storage::disk('public')->exists($rem)) {
                    Storage::disk('public')->delete($rem);
                }
            }
            $currentGallery = array_values(array_diff($currentGallery, $toRemove));
        }
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $gFile) {
                $gName = Str::random(32) . '.' . strtolower($gFile->getClientOriginalExtension());
                $currentGallery[] = $gFile->storeAs('devices/gallery', $gName, 'public');
            }
        }
        $validated['image_gallery'] = !empty($currentGallery) ? $currentGallery : null;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        DB::transaction(function () use ($device, $validated) {
            $device->update($validated);

            // Sync model numbers: delete old and re-insert
            $device->modelNumbers()->delete();
            if (!empty($validated['model_numbers'])) {
                foreach ($validated['model_numbers'] as $m) {
                    if (!empty($m['model_number'])) {
                        DeviceModelNumber::create([
                            'device_id' => $device->id,
                            'model_number' => trim($m['model_number']),
                            'region' => $m['region'] ?? null,
                            'is_primary' => isset($m['is_primary']) && (bool)$m['is_primary'],
                        ]);
                    }
                }
            }

            // Sync variants: delete old and re-insert
            $device->variants()->delete();
            if (!empty($validated['variants'])) {
                foreach ($validated['variants'] as $v) {
                    if (!empty($v['name'])) {
                        DeviceVariant::create([
                            'device_id' => $device->id,
                            'name' => trim($v['name']),
                            'ram' => $v['ram'] ?? null,
                            'internal_storage' => $v['internal_storage'] ?? null,
                            'color' => $v['color'] ?? null,
                            'notes' => $v['notes'] ?? null,
                        ]);
                    }
                }
            }

            // Sync dynamic specification values: update or delete empty
            if (isset($validated['specs'])) {
                foreach ($validated['specs'] as $fieldId => $specVal) {
                    if ($specVal !== null && trim($specVal) !== '') {
                        DeviceSpecificationValue::updateOrCreate(
                            [
                                'device_id' => $device->id,
                                'field_id' => (int) $fieldId,
                            ],
                            [
                                'value' => trim($specVal),
                            ]
                        );
                    } else {
                        DeviceSpecificationValue::where('device_id', $device->id)
                            ->where('field_id', (int) $fieldId)
                            ->delete();
                    }
                }
            }
        });

        ActivityLogger::log('update', $device, ['name' => $device->name, 'status' => $device->status]);

        return redirect()->route('admin.devices.index')
            ->with('success', "Device '{$device->name}' updated successfully.");
    }

    public function destroy(Device $device): RedirectResponse
    {
        Gate::authorize('delete', $device);

        $name = $device->name;
        $device->delete();

        ActivityLogger::log('delete', $device, ['name' => $name]);

        return redirect()->route('admin.devices.index')
            ->with('success', "Device '{$name}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $device = Device::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $device);

        $device->restore();

        ActivityLogger::log('restore', $device, ['name' => $device->name]);

        return redirect()->route('admin.devices.index', ['trashed' => 1])
            ->with('success', "Device '{$device->name}' restored successfully.");
    }
}
