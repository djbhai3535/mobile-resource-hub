<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderBy('is_system', 'desc')->orderBy('title')->get();

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:191', Rule::unique('pages', 'slug')->ignore($page->id)],
            'content' => ['required', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'og_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:published,draft'],
        ]);

        // Protect system slugs from modification to preserve core public URLs
        if ($page->is_system) {
            unset($validated['slug']);
        }

        $page->update($validated);

        ActivityLogger::log('update', $page, ['title' => $page->title, 'slug' => $page->slug]);

        return redirect()->route('admin.pages.index')->with('success', "Page '{$page->title}' updated successfully.");
    }
}
