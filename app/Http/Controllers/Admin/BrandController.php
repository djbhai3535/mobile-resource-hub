<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Brand::class);

        $query = Brand::withCount('devices');

        if ($request->filled('q')) {
            $keyword = '%' . $request->q . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword)
                    ->orWhere('country_of_origin', 'like', $keyword);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        $brands = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'total' => Brand::count(),
            'published' => Brand::where('status', 'published')->count(),
            'draft' => Brand::where('status', 'draft')->count(),
            'archived' => Brand::where('status', 'archived')->count(),
            'trashed' => Brand::onlyTrashed()->count(),
        ];

        return view('admin.brands.index', compact('brands', 'counts'));
    }

    public function create(): View
    {
        Gate::authorize('create', Brand::class);

        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Brand::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:brands,name'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:brands,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'full_description' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = Str::random(32) . '.' . strtolower($file->getClientOriginalExtension());
            $validated['logo'] = $file->storeAs('brands', $fileName, 'public');
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $brand = Brand::create($validated);

        ActivityLogger::log('create', $brand, ['name' => $brand->name, 'status' => $brand->status]);

        return redirect()->route('admin.brands.index')
            ->with('success', "Brand '{$brand->name}' created successfully.");
    }

    public function edit(Brand $brand): View
    {
        Gate::authorize('update', $brand);

        $brand->loadCount('devices');

        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        Gate::authorize('update', $brand);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('brands', 'name')->ignore($brand->id)],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('brands', 'slug')->ignore($brand->id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'full_description' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->boolean('remove_logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            $file = $request->file('logo');
            $fileName = Str::random(32) . '.' . strtolower($file->getClientOriginalExtension());
            $validated['logo'] = $file->storeAs('brands', $fileName, 'public');
        } else {
            unset($validated['logo']);
        }

        unset($validated['remove_logo']);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $brand->update($validated);

        ActivityLogger::log('update', $brand, ['name' => $brand->name, 'status' => $brand->status]);

        return redirect()->route('admin.brands.index')
            ->with('success', "Brand '{$brand->name}' updated successfully.");
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        Gate::authorize('delete', $brand);

        // Verification: prevent accidental deletion if brand has devices
        $deviceCount = $brand->devices()->count();
        if ($deviceCount > 0) {
            return back()->with('error', "Cannot delete brand '{$brand->name}' because it contains {$deviceCount} associated device(s). Please reassign or delete these devices before deleting the brand.");
        }

        $name = $brand->name;
        $brand->delete();

        ActivityLogger::log('delete', $brand, ['name' => $name]);

        return redirect()->route('admin.brands.index')
            ->with('success', "Brand '{$name}' moved to trash.");
    }

    public function restore(int $id): RedirectResponse
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $brand);

        $brand->restore();

        ActivityLogger::log('restore', $brand, ['name' => $brand->name]);

        return redirect()->route('admin.brands.index', ['trashed' => 1])
            ->with('success', "Brand '{$brand->name}' restored successfully.");
    }
}
