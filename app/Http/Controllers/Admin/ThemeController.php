<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\ThemeVersion;
use App\Services\ThemeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeController extends Controller
{
    /**
     * Display Split-Screen Theme Customizer.
     */
    public function index(ThemeService $themeService): View
    {
        $theme = $themeService->getActiveTheme();
        $draftSettings = $themeService->getDraftSettings($theme->id);
        $publishedSettings = $themeService->getPublishedSettings();
        $recentVersions = $theme->versions()->take(5)->get();

        return view('admin.theme.index', compact(
            'theme',
            'draftSettings',
            'publishedSettings',
            'recentVersions'
        ));
    }

    /**
     * Save Draft Settings without altering public website.
     */
    public function saveDraft(Request $request, ThemeService $themeService): JsonResponse
    {
        $validated = $this->validateThemeSettings($request);

        $draft = $themeService->saveDraft($validated, null, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Theme draft settings saved successfully.',
            'settings' => $draft,
        ]);
    }

    /**
     * Publish the current Draft to become the Active Live Theme.
     */
    public function publish(Request $request, ThemeService $themeService): RedirectResponse
    {
        $summary = $request->input('change_summary', 'Theme customized and published via Admin Panel');

        // If form inputs are provided directly with publish
        if ($request->has('global_colors')) {
            $validated = $this->validateThemeSettings($request);
            $themeService->saveDraft($validated, null, auth()->id());
        }

        $version = $themeService->publishDraft(null, auth()->id(), $summary);

        return redirect()->route('admin.theme.index')
            ->with('success', "Theme Version {$version->version_number} published successfully! Live website styles updated.");
    }

    /**
     * Reset Draft to current published state.
     */
    public function resetDraft(ThemeService $themeService): RedirectResponse
    {
        $themeService->resetDraft();

        return redirect()->route('admin.theme.index')
            ->with('success', 'Draft settings reset to currently active published theme.');
    }

    /**
     * Live Preview Renderer for Iframe.
     */
    public function preview(Request $request, ThemeService $themeService): View
    {
        $draftSettings = $themeService->getDraftSettings();
        $draftCss = $themeService->generateCssVariables($draftSettings);

        // Render actual homepage template with draft CSS overrides
        return view('admin.theme.preview', compact('draftSettings', 'draftCss'));
    }

    /**
     * Theme Version History.
     */
    public function history(ThemeService $themeService): View
    {
        $theme = $themeService->getActiveTheme();
        $versions = $theme->versions()->paginate(15);

        return view('admin.theme.history', compact('theme', 'versions'));
    }

    /**
     * Restore previous theme version.
     */
    public function restore(Request $request, int $versionId, ThemeService $themeService): RedirectResponse
    {
        $version = ThemeVersion::findOrFail($versionId);
        $themeService->restoreVersion($version->id, auth()->id());

        return redirect()->route('admin.theme.index')
            ->with('success', "Successfully restored and published theme settings from Version {$version->version_number}.");
    }

    /**
     * Validate all theme settings with strict types and boundaries.
     */
    protected function validateThemeSettings(Request $request): array
    {
        // Color hex regex
        $colorRule = ['nullable', 'string', 'max:50', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$|^rgba?\([0-9\s,\.]+\)$/'];

        return $request->validate([
            'branding.site_name' => 'nullable|string|max:100',
            'branding.tagline' => 'nullable|string|max:200',
            'branding.logo_url' => 'nullable|string|max:255',
            'branding.dark_logo_url' => 'nullable|string|max:255',
            'branding.light_logo_url' => 'nullable|string|max:255',
            'branding.favicon_url' => 'nullable|string|max:255',
            'branding.social_image_url' => 'nullable|string|max:255',

            'global_colors.primary' => $colorRule,
            'global_colors.primary_hover' => $colorRule,
            'global_colors.secondary' => $colorRule,
            'global_colors.accent' => $colorRule,
            'global_colors.success' => $colorRule,
            'global_colors.warning' => $colorRule,
            'global_colors.error' => $colorRule,
            'global_colors.link' => $colorRule,

            'light_palette.bg_app' => $colorRule,
            'light_palette.surface' => $colorRule,
            'light_palette.surface_secondary' => $colorRule,
            'light_palette.surface_glass' => $colorRule,
            'light_palette.surface_glass_border' => $colorRule,
            'light_palette.glass_header_bg' => $colorRule,
            'light_palette.text_main' => $colorRule,
            'light_palette.text_muted' => $colorRule,
            'light_palette.border_subtle' => $colorRule,

            'dark_palette.bg_app' => $colorRule,
            'dark_palette.surface' => $colorRule,
            'dark_palette.surface_secondary' => $colorRule,
            'dark_palette.surface_glass' => $colorRule,
            'dark_palette.surface_glass_border' => $colorRule,
            'dark_palette.glass_header_bg' => $colorRule,
            'dark_palette.text_main' => $colorRule,
            'dark_palette.text_muted' => $colorRule,
            'dark_palette.border_subtle' => $colorRule,
            'dark_palette.primary' => $colorRule,
            'dark_palette.primary_hover' => $colorRule,

            'typography.font_family' => 'nullable|string|max:100',
            'typography.heading_font' => 'nullable|string|max:100',
            'typography.base_font_size' => 'nullable|string|max:20',
            'typography.heading_scale' => 'nullable|string|max:20',
            'typography.line_height' => 'nullable|string|max:20',

            'layout.container_max_width' => 'nullable|string|max:20',
            'layout.header_height' => 'nullable|string|max:20',
            'layout.border_radius_md' => 'nullable|string|max:20',
            'layout.border_radius_lg' => 'nullable|string|max:20',

            'glassmorphism.blur_intensity' => 'nullable|string|max:20',
            'glassmorphism.card_transparency' => 'nullable|string|max:20',
            'glassmorphism.glass_border_opacity' => 'nullable|string|max:20',
            'glassmorphism.hover_glow' => $colorRule,

            'buttons.border_radius' => 'nullable|string|max:20',
            'buttons.hover_effect' => 'nullable|string|in:lift,glow,none',

            'animations.enabled' => 'nullable|boolean',
            'animations.card_hover' => 'nullable|boolean',
            'animations.nav_transitions' => 'nullable|boolean',
        ]);
    }
}
