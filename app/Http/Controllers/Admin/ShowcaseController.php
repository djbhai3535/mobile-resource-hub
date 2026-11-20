<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShowcaseItem;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShowcaseController extends Controller
{
    /**
     * Display a listing of 3D Mobile Showcase videos.
     */
    public function index(): View
    {
        $items = ShowcaseItem::ordered()->get();

        return view('admin.showcase.index', compact('items'));
    }

    /**
     * Show form for uploading a new 3D showcase video.
     */
    public function create(): View
    {
        return view('admin.showcase.create');
    }

    /**
     * Store a newly uploaded 3D showcase video.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:100',
            'video' => 'required|file|mimes:mp4,webm|max:51200', // up to 50MB
            'poster' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:5120', // up to 5MB
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
            'autoplay' => 'nullable|boolean',
            'loop' => 'nullable|boolean',
            'muted' => 'nullable|boolean',
        ]);

        $videoFile = $request->file('video');
        $videoExt = strtolower($videoFile->getClientOriginalExtension());
        $videoFileName = 'showcase_' . time() . '_' . Str::random(8) . '.' . $videoExt;
        $videoPath = $videoFile->storeAs('showcase', $videoFileName, 'public');

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterFile = $request->file('poster');
            $posterExt = strtolower($posterFile->getClientOriginalExtension());
            $posterFileName = 'poster_' . time() . '_' . Str::random(8) . '.' . $posterExt;
            $posterPath = $posterFile->storeAs('showcase', $posterFileName, 'public');
        }

        $item = ShowcaseItem::create([
            'title' => trim($validated['title']),
            'subtitle' => $request->filled('subtitle') ? trim($validated['subtitle']) : null,
            'brand_name' => $request->filled('brand_name') ? trim($validated['brand_name']) : null,
            'video_path' => $videoPath,
            'video_format' => $videoExt,
            'poster_path' => $posterPath,
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => $request->boolean('is_active', true),
            'autoplay' => $request->boolean('autoplay', true),
            'loop' => $request->boolean('loop', true),
            'muted' => $request->boolean('muted', true),
        ]);

        ActivityLogger::log('showcase_create', $item, [
            'description' => 'Uploaded 3D Mobile Showcase item: ' . $item->title,
            'video_path' => $item->video_path,
        ]);

        return redirect()->route('admin.showcase.index')->with('success', '3D Showcase video uploaded and configured successfully!');
    }

    /**
     * Show form for editing an existing 3D showcase item.
     */
    public function edit(ShowcaseItem $showcase): View
    {
        return view('admin.showcase.edit', compact('showcase'));
    }

    /**
     * Update an existing 3D showcase item.
     */
    public function update(Request $request, ShowcaseItem $showcase): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'brand_name' => 'nullable|string|max:100',
            'video' => 'nullable|file|mimes:mp4,webm|max:51200',
            'poster' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'remove_poster' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'nullable|boolean',
            'autoplay' => 'nullable|boolean',
            'loop' => 'nullable|boolean',
            'muted' => 'nullable|boolean',
        ]);

        // Replace video if new file uploaded
        if ($request->hasFile('video')) {
            if ($showcase->video_path && Storage::disk('public')->exists($showcase->video_path)) {
                Storage::disk('public')->delete($showcase->video_path);
            }

            $videoFile = $request->file('video');
            $videoExt = strtolower($videoFile->getClientOriginalExtension());
            $videoFileName = 'showcase_' . time() . '_' . Str::random(8) . '.' . $videoExt;
            $showcase->video_path = $videoFile->storeAs('showcase', $videoFileName, 'public');
            $showcase->video_format = $videoExt;
        }

        // Handle poster removal or replacement
        if ($request->boolean('remove_poster')) {
            if ($showcase->poster_path && Storage::disk('public')->exists($showcase->poster_path)) {
                Storage::disk('public')->delete($showcase->poster_path);
            }
            $showcase->poster_path = null;
        } elseif ($request->hasFile('poster')) {
            if ($showcase->poster_path && Storage::disk('public')->exists($showcase->poster_path)) {
                Storage::disk('public')->delete($showcase->poster_path);
            }

            $posterFile = $request->file('poster');
            $posterExt = strtolower($posterFile->getClientOriginalExtension());
            $posterFileName = 'poster_' . time() . '_' . Str::random(8) . '.' . $posterExt;
            $showcase->poster_path = $posterFile->storeAs('showcase', $posterFileName, 'public');
        }

        $showcase->title = trim($validated['title']);
        $showcase->subtitle = $request->filled('subtitle') ? trim($validated['subtitle']) : null;
        $showcase->brand_name = $request->filled('brand_name') ? trim($validated['brand_name']) : null;
        $showcase->sort_order = (int) ($validated['sort_order'] ?? 0);
        $showcase->is_active = $request->boolean('is_active');
        $showcase->autoplay = $request->boolean('autoplay');
        $showcase->loop = $request->boolean('loop');
        $showcase->muted = $request->boolean('muted');
        $showcase->save();

        ActivityLogger::log('showcase_update', $showcase, [
            'description' => 'Updated 3D Mobile Showcase item: ' . $showcase->title,
        ]);

        return redirect()->route('admin.showcase.index')->with('success', '3D Showcase video updated successfully!');
    }

    /**
     * Remove a 3D showcase video and delete files.
     */
    public function destroy(ShowcaseItem $showcase): RedirectResponse
    {
        $title = $showcase->title;

        if ($showcase->video_path && Storage::disk('public')->exists($showcase->video_path)) {
            Storage::disk('public')->delete($showcase->video_path);
        }

        if ($showcase->poster_path && Storage::disk('public')->exists($showcase->poster_path)) {
            Storage::disk('public')->delete($showcase->poster_path);
        }

        $showcase->delete();

        ActivityLogger::log('showcase_delete', null, [
            'description' => 'Deleted 3D Mobile Showcase item: ' . $title,
        ]);

        return redirect()->route('admin.showcase.index')->with('success', '3D Showcase video removed successfully!');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggle(ShowcaseItem $showcase): RedirectResponse
    {
        $showcase->is_active = !$showcase->is_active;
        $showcase->save();

        $statusText = $showcase->is_active ? 'enabled' : 'disabled';

        ActivityLogger::log('showcase_toggle', $showcase, [
            'description' => "Showcase video {$showcase->title} {$statusText}",
            'is_active' => $showcase->is_active,
        ]);

        return back()->with('success', "Showcase video {$showcase->title} is now {$statusText}.");
    }
}
