<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Services\HomepageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    /**
     * List and manage homepage sections.
     */
    public function index(HomepageService $homepageService): View
    {
        $sections = $homepageService->getOrderedSections(false);

        return view('admin.homepage.index', compact('sections'));
    }

    /**
     * Update section configuration.
     */
    public function update(Request $request, int $id, HomepageService $homepageService): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'item_count' => 'required|integer|min:1|max:24',
            'display_style' => 'required|string|in:grid,carousel,list,compact,default,ad',
            'background_style' => 'required|string|in:default,subtle,glass,dark',
            'category_filter' => 'nullable|string|max:100',
        ]);

        $homepageService->updateSection($id, $validated);

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Homepage section configuration updated successfully.');
    }

    /**
     * Toggle section status (Active/Inactive).
     */
    public function toggle(int $id, HomepageService $homepageService): JsonResponse
    {
        $newState = $homepageService->toggleSection($id);

        return response()->json([
            'success' => true,
            'is_enabled' => $newState,
            'message' => $newState ? 'Section enabled on homepage.' : 'Section disabled on homepage.',
        ]);
    }

    /**
     * Save new display order.
     */
    public function reorder(Request $request, HomepageService $homepageService): JsonResponse
    {
        $validated = $request->validate([
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:homepage_sections,id',
        ]);

        $homepageService->reorder($validated['ordered_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Homepage sections order updated successfully.',
        ]);
    }
}
