<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceSpecificationValue;
use App\Models\SpecificationField;
use App\Models\SpecificationGroup;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SpecificationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('specifications.manage');

        $groups = SpecificationGroup::with(['fields' => function ($query) {
            $query->orderBy('sort_order')->orderBy('label');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view('admin.specifications.index', compact('groups'));
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:191', 'unique:specification_groups,slug'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $group = SpecificationGroup::create($validated);

        ActivityLogger::log('create', $group, ['name' => $group->name]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification group '{$group->name}' created successfully.");
    }

    public function updateGroup(Request $request, SpecificationGroup $group): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('specification_groups', 'slug')->ignore($group->id)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $group->update($validated);

        ActivityLogger::log('update', $group, ['name' => $group->name]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification group '{$group->name}' updated successfully.");
    }

    public function destroyGroup(SpecificationGroup $group): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        $fieldsCount = $group->fields()->count();
        if ($fieldsCount > 0) {
            return back()->with('error', "Cannot delete group '{$group->name}' because it contains {$fieldsCount} specification fields. Please delete or reassign the fields first.");
        }

        $name = $group->name;
        $group->delete();

        ActivityLogger::log('delete', $group, ['name' => $name]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification group '{$name}' deleted.");
    }

    public function storeField(Request $request): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        $validated = $request->validate([
            'group_id' => ['required', 'exists:specification_groups,id'],
            'label' => ['required', 'string', 'max:150'],
            'key' => ['nullable', 'string', 'max:191', 'unique:specification_fields,key'],
            'field_type' => ['required', Rule::in(['text', 'textarea', 'number', 'boolean', 'date', 'url', 'select', 'multivalue'])],
            'unit' => ['nullable', 'string', 'max:50'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'show_on_public' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['label'], '_');
        }

        // Process options into array if present
        if (!empty($validated['options'])) {
            $validated['options'] = array_map('trim', explode(',', $validated['options']));
        } else {
            $validated['options'] = null;
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['show_on_public'] = $request->has('show_on_public');
        $validated['is_filterable'] = $request->has('is_filterable');
        $validated['is_active'] = $request->has('is_active');

        $field = SpecificationField::create($validated);

        ActivityLogger::log('create', $field, ['label' => $field->label, 'key' => $field->key]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification field '{$field->label}' created successfully.");
    }

    public function updateField(Request $request, SpecificationField $field): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        $validated = $request->validate([
            'group_id' => ['required', 'exists:specification_groups,id'],
            'label' => ['required', 'string', 'max:150'],
            'key' => ['nullable', 'string', 'max:191', Rule::unique('specification_fields', 'key')->ignore($field->id)],
            'field_type' => ['required', Rule::in(['text', 'textarea', 'number', 'boolean', 'date', 'url', 'select', 'multivalue'])],
            'unit' => ['nullable', 'string', 'max:50'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'show_on_public' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (empty($validated['key'])) {
            $validated['key'] = Str::slug($validated['label'], '_');
        }

        if (!empty($validated['options'])) {
            $validated['options'] = array_map('trim', explode(',', $validated['options']));
        } else {
            $validated['options'] = null;
        }

        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['show_on_public'] = $request->has('show_on_public');
        $validated['is_filterable'] = $request->has('is_filterable');
        $validated['is_active'] = $request->has('is_active');

        $field->update($validated);

        ActivityLogger::log('update', $field, ['label' => $field->label, 'key' => $field->key]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification field '{$field->label}' updated successfully.");
    }

    public function destroyField(SpecificationField $field): RedirectResponse
    {
        Gate::authorize('specifications.manage');

        // Check if values exist across devices
        $valuesCount = DeviceSpecificationValue::where('field_id', $field->id)->count();
        if ($valuesCount > 0) {
            return back()->with('error', "Cannot delete field '{$field->label}' because {$valuesCount} device(s) have recorded values for this field. Clear device values before deleting.");
        }

        $label = $field->label;
        $field->delete();

        ActivityLogger::log('delete', $field, ['label' => $label]);

        return redirect()->route('admin.specifications.index')
            ->with('success', "Specification field '{$label}' deleted.");
    }
}
