<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::with(['items.children'])->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $menu->update($validated);

        ActivityLogger::log('update', $menu, ['name' => $menu->name]);

        return back()->with('success', "Menu '{$menu->name}' updated.");
    }

    public function storeItem(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['required', 'in:_self,_blank'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->sanitizeUrl($validated['url']);

        $item = $menu->items()->create([
            'label' => $validated['label'],
            'url' => $validated['url'],
            'target' => $validated['target'],
            'order' => $validated['order'] ?? ($menu->items()->max('order') + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log('create', $item, ['label' => $item->label, 'menu' => $menu->location]);

        return back()->with('success', "Menu item '{$item->label}' added.");
    }

    public function updateItem(Request $request, MenuItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['required', 'string', 'max:255'],
            'target' => ['required', 'in:_self,_blank'],
            'order' => ['required', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->sanitizeUrl($validated['url']);

        $item->update([
            'label' => $validated['label'],
            'url' => $validated['url'],
            'target' => $validated['target'],
            'order' => $validated['order'],
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLogger::log('update', $item, ['label' => $item->label]);

        return back()->with('success', "Menu item '{$item->label}' updated.");
    }

    public function destroyItem(MenuItem $item): RedirectResponse
    {
        $label = $item->label;
        $item->delete();

        ActivityLogger::log('delete', $item, ['label' => $label]);

        return back()->with('success', "Menu item '{$label}' removed.");
    }

    protected function sanitizeUrl(string $url): void
    {
        $lower = strtolower(trim($url));
        if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') || str_starts_with($lower, 'vbscript:')) {
            throw ValidationException::withMessages([
                'url' => 'Security Error: Malicious or executable URI schemes are strictly prohibited.',
            ]);
        }
    }
}
