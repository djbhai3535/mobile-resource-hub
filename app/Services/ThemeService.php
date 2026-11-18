<?php

namespace App\Services;

use App\Models\Theme;
use App\Models\ThemeSetting;
use App\Models\ThemeVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ThemeService
{
    public const CACHE_KEY_PUBLISHED_CSS = 'mrh_theme_published_css';
    public const CACHE_KEY_PUBLISHED_SETTINGS = 'mrh_theme_published_settings';

    /**
     * Default baseline settings reflecting the glassmorphism design tokens.
     */
    public function getDefaultSettings(): array
    {
        return [
            'branding' => [
                'site_name' => 'Mobile Resource Hub',
                'tagline' => 'Verified Phone Specs, Firmware, Drivers & Guides',
                'logo_url' => null,
                'dark_logo_url' => null,
                'light_logo_url' => null,
                'favicon_url' => null,
                'social_image_url' => null,
            ],
            'global_colors' => [
                'primary' => '#2563EB',
                'primary_hover' => '#1D4ED8',
                'secondary' => '#475569',
                'accent' => '#3B82F6',
                'success' => '#10B981',
                'warning' => '#F59E0B',
                'error' => '#EF4444',
                'link' => '#2563EB',
            ],
            'light_palette' => [
                'bg_app' => '#F5F7FB',
                'surface' => '#FFFFFF',
                'surface_secondary' => '#F8FAFC',
                'surface_glass' => 'rgba(255, 255, 255, 0.82)',
                'surface_glass_border' => 'rgba(226, 232, 240, 0.85)',
                'glass_header_bg' => 'rgba(255, 255, 255, 0.85)',
                'text_main' => '#172033',
                'text_muted' => '#64748B',
                'border_subtle' => '#E2E8F0',
                'card_shadow' => '0 4px 20px -2px rgba(15, 23, 42, 0.06)',
                'card_hover_shadow' => '0 12px 28px -4px rgba(15, 23, 42, 0.12)',
            ],
            'dark_palette' => [
                'bg_app' => '#0B1220',
                'surface' => '#141E30',
                'surface_secondary' => '#1A263D',
                'surface_glass' => 'rgba(20, 30, 48, 0.85)',
                'surface_glass_border' => 'rgba(38, 52, 73, 0.85)',
                'glass_header_bg' => 'rgba(11, 18, 32, 0.88)',
                'text_main' => '#E5EDF8',
                'text_muted' => '#94A3B8',
                'border_subtle' => '#263449',
                'primary' => '#60A5FA',
                'primary_hover' => '#93C5FD',
                'card_shadow' => '0 4px 24px -2px rgba(0, 0, 0, 0.45)',
                'card_hover_shadow' => '0 14px 32px -4px rgba(0, 0, 0, 0.65)',
            ],
            'typography' => [
                'font_family' => "'Inter', system-ui, -apple-system, sans-serif",
                'heading_font' => "'Inter', system-ui, -apple-system, sans-serif",
                'base_font_size' => '15px',
                'heading_scale' => '1.25',
                'line_height' => '1.6',
                'font_weight_normal' => '400',
                'font_weight_bold' => '700',
            ],
            'layout' => [
                'container_max_width' => '1320px',
                'header_height' => '70px',
                'content_spacing' => '1.5rem',
                'card_spacing' => '1rem',
                'border_radius_sm' => '8px',
                'border_radius_md' => '12px',
                'border_radius_lg' => '16px',
                'border_radius_xl' => '20px',
            ],
            'glassmorphism' => [
                'blur_intensity' => '16px',
                'card_transparency' => '0.85',
                'glass_border_opacity' => '0.85',
                'glass_shadow' => '0 8px 32px 0 rgba(31, 38, 135, 0.08)',
                'hover_glow' => 'rgba(37, 99, 235, 0.15)',
            ],
            'buttons' => [
                'border_radius' => '50px', // rounded-pill style
                'size' => 'md',
                'hover_effect' => 'lift', // lift, glow, none
                'icon_placement' => 'left',
            ],
            'animations' => [
                'enabled' => true,
                'card_hover' => true,
                'nav_transitions' => true,
                'button_transitions' => true,
            ],
        ];
    }

    /**
     * Retrieve or initialize the active theme.
     */
    public function getActiveTheme(): Theme
    {
        $theme = Theme::active()->first();

        if (!$theme) {
            $theme = Theme::create([
                'name' => 'Default Glass Theme',
                'slug' => 'default-glass-theme',
                'is_active' => true,
                'version' => 1,
            ]);

            $defaults = $this->getDefaultSettings();

            // Create both published and draft baseline records
            ThemeSetting::create(array_merge($defaults, [
                'theme_id' => $theme->id,
                'is_published' => true,
            ]));

            ThemeSetting::create(array_merge($defaults, [
                'theme_id' => $theme->id,
                'is_published' => false,
            ]));
        }

        return $theme;
    }

    /**
     * Get active published theme settings (cached).
     *
     * Defensive: if cache returns a non-array (e.g. __PHP_Incomplete_Class from
     * stale serialized data) the cache is flushed and rebuilt from DB.
     */
    public function getPublishedSettings(): array
    {
        try {
            $cached = Cache::get(self::CACHE_KEY_PUBLISHED_SETTINGS);
            if ($cached !== null) {
                if (!is_array($cached)) {
                    throw new \RuntimeException('Cached theme settings are not an array — flushing.');
                }
                return $cached;
            }
        } catch (\Throwable $e) {
            Cache::forget(self::CACHE_KEY_PUBLISHED_SETTINGS);
        }

        try {
            $theme = $this->getActiveTheme();
            $setting = $theme->publishedSettings;
            $settings = $setting ? $this->normalizeSettings($setting) : $this->getDefaultSettings();
        } catch (\Throwable $e) {
            $settings = $this->getDefaultSettings();
        }

        try {
            Cache::put(self::CACHE_KEY_PUBLISHED_SETTINGS, $settings, 86400);
        } catch (\Throwable $e) {
            // Cache write failure must never crash public pages
        }

        return $settings;
    }

    /**
     * Get current draft settings for customizer editing.
     */
    public function getDraftSettings(?int $themeId = null): array
    {
        $theme = $themeId ? Theme::findOrFail($themeId) : $this->getActiveTheme();
        $draft = $theme->draftSettings;

        if (!$draft) {
            $published = $theme->publishedSettings;
            $data = $published ? $this->normalizeSettings($published) : $this->getDefaultSettings();
            $draft = ThemeSetting::create(array_merge($data, [
                'theme_id' => $theme->id,
                'is_published' => false,
            ]));
        }

        return $this->normalizeSettings($draft);
    }

    /**
     * Save draft modifications without changing the live website.
     */
    public function saveDraft(array $validated, ?int $themeId = null, ?int $userId = null): ThemeSetting
    {
        $theme = $themeId ? Theme::findOrFail($themeId) : $this->getActiveTheme();

        if ($userId) {
            $theme->update(['updated_by' => $userId]);
        }

        $draft = ThemeSetting::updateOrCreate(
            ['theme_id' => $theme->id, 'is_published' => false],
            [
                'branding' => $validated['branding'] ?? [],
                'global_colors' => $validated['global_colors'] ?? [],
                'light_palette' => $validated['light_palette'] ?? [],
                'dark_palette' => $validated['dark_palette'] ?? [],
                'typography' => $validated['typography'] ?? [],
                'layout' => $validated['layout'] ?? [],
                'glassmorphism' => $validated['glassmorphism'] ?? [],
                'buttons' => $validated['buttons'] ?? [],
                'animations' => $validated['animations'] ?? [],
            ]
        );

        return $draft;
    }

    /**
     * Publish the current draft theme into the live website.
     */
    public function publishDraft(?int $themeId = null, ?int $userId = null, ?string $summary = null): ThemeVersion
    {
        $theme = $themeId ? Theme::findOrFail($themeId) : $this->getActiveTheme();
        $draft = $theme->draftSettings;

        if (!$draft) {
            $draft = $this->saveDraft($this->getDefaultSettings(), $theme->id, $userId);
        }

        $snapshot = $this->normalizeSettings($draft);

        return DB::transaction(function () use ($theme, $draft, $snapshot, $userId, $summary) {
            // Update or create published record
            ThemeSetting::updateOrCreate(
                ['theme_id' => $theme->id, 'is_published' => true],
                [
                    'branding' => $snapshot['branding'],
                    'global_colors' => $snapshot['global_colors'],
                    'light_palette' => $snapshot['light_palette'],
                    'dark_palette' => $snapshot['dark_palette'],
                    'typography' => $snapshot['typography'],
                    'layout' => $snapshot['layout'],
                    'glassmorphism' => $snapshot['glassmorphism'],
                    'buttons' => $snapshot['buttons'],
                    'animations' => $snapshot['animations'],
                ]
            );

            // Increment theme version
            $nextVersion = $theme->version + 1;
            $theme->update([
                'version' => $nextVersion,
                'updated_by' => $userId,
            ]);

            // Save historical snapshot
            $versionRecord = ThemeVersion::create([
                'theme_id' => $theme->id,
                'version_number' => $nextVersion,
                'settings_snapshot' => $snapshot,
                'change_summary' => $summary ?: "Theme Version {$nextVersion} published",
                'published_by' => $userId,
                'published_at' => now(),
            ]);

            $this->clearCache();

            return $versionRecord;
        });
    }

    /**
     * Restore a previous theme version from history.
     */
    public function restoreVersion(int $versionId, ?int $userId = null): Theme
    {
        $version = ThemeVersion::findOrFail($versionId);
        $snapshot = $version->settings_snapshot;
        $theme = $version->theme;

        DB::transaction(function () use ($theme, $snapshot, $userId, $version) {
            // Overwrite draft settings
            $this->saveDraft($snapshot, $theme->id, $userId);

            // Publish immediately
            $this->publishDraft($theme->id, $userId, "Restored to Version {$version->version_number}");
        });

        return $theme;
    }

    /**
     * Reset draft to currently published settings.
     */
    public function resetDraft(?int $themeId = null): void
    {
        $theme = $themeId ? Theme::findOrFail($themeId) : $this->getActiveTheme();
        $published = $theme->publishedSettings;

        $data = $published ? $this->normalizeSettings($published) : $this->getDefaultSettings();

        ThemeSetting::updateOrCreate(
            ['theme_id' => $theme->id, 'is_published' => false],
            $data
        );
    }

    /**
     * Generate CSS variable overrides based on theme settings.
     */
    public function generateCssVariables(array $settings): string
    {
        $gc = $settings['global_colors'] ?? [];
        $lp = $settings['light_palette'] ?? [];
        $dp = $settings['dark_palette'] ?? [];
        $ty = $settings['typography'] ?? [];
        $ly = $settings['layout'] ?? [];
        $gl = $settings['glassmorphism'] ?? [];
        $bt = $settings['buttons'] ?? [];

        $primary = $gc['primary'] ?? '#2563EB';
        $primaryHover = $gc['primary_hover'] ?? '#1D4ED8';
        $secondary = $gc['secondary'] ?? '#475569';
        $fontMain = $ty['font_family'] ?? "'Inter', system-ui, sans-serif";
        $fontSize = $ty['base_font_size'] ?? '15px';
        $lineHeight = $ty['line_height'] ?? '1.6';

        $css = ":root {\n";
        $css .= "  --primary: {$primary};\n";
        $css .= "  --primary-hover: {$primaryHover};\n";
        $css .= "  --secondary: {$secondary};\n";
        if (!empty($gc['accent'])) $css .= "  --accent: {$gc['accent']};\n";
        if (!empty($gc['success'])) $css .= "  --success: {$gc['success']};\n";
        if (!empty($gc['warning'])) $css .= "  --warning: {$gc['warning']};\n";
        if (!empty($gc['error'])) $css .= "  --danger: {$gc['error']};\n";

        // Light palette
        if (!empty($lp['bg_app'])) $css .= "  --bg-app: {$lp['bg_app']};\n";
        if (!empty($lp['surface'])) $css .= "  --surface: {$lp['surface']};\n";
        if (!empty($lp['text_main'])) $css .= "  --text-main: {$lp['text_main']};\n";
        if (!empty($lp['text_muted'])) $css .= "  --text-muted: {$lp['text_muted']};\n";
        if (!empty($lp['border_subtle'])) $css .= "  --border-subtle: {$lp['border_subtle']};\n";
        if (!empty($lp['surface_glass'])) $css .= "  --surface-glass: {$lp['surface_glass']};\n";
        if (!empty($lp['glass_header_bg'])) $css .= "  --glass-header-bg: {$lp['glass_header_bg']};\n";

        // Typography & Layout
        $css .= "  --font-main: {$fontMain};\n";
        $css .= "  --font-size-base: {$fontSize};\n";
        $css .= "  --line-height-base: {$lineHeight};\n";

        if (!empty($ly['container_max_width'])) $css .= "  --container-max-width: {$ly['container_max_width']};\n";
        if (!empty($ly['border_radius_md'])) $css .= "  --radius-md: {$ly['border_radius_md']};\n";
        if (!empty($ly['border_radius_lg'])) $css .= "  --radius-lg: {$ly['border_radius_lg']};\n";
        if (!empty($gl['blur_intensity'])) $css .= "  --glass-blur: {$gl['blur_intensity']};\n";
        if (!empty($bt['border_radius'])) $css .= "  --btn-radius: {$bt['border_radius']};\n";

        $css .= "}\n\n";

        // Dark Palette Overrides
        $css .= "[data-bs-theme=\"dark\"] {\n";
        if (!empty($dp['bg_app'])) $css .= "  --bg-app: {$dp['bg_app']};\n";
        if (!empty($dp['surface'])) $css .= "  --surface: {$dp['surface']};\n";
        if (!empty($dp['text_main'])) $css .= "  --text-main: {$dp['text_main']};\n";
        if (!empty($dp['text_muted'])) $css .= "  --text-muted: {$dp['text_muted']};\n";
        if (!empty($dp['border_subtle'])) $css .= "  --border-subtle: {$dp['border_subtle']};\n";
        if (!empty($dp['surface_glass'])) $css .= "  --surface-glass: {$dp['surface_glass']};\n";
        if (!empty($dp['glass_header_bg'])) $css .= "  --glass-header-bg: {$dp['glass_header_bg']};\n";
        if (!empty($dp['primary'])) $css .= "  --primary: {$dp['primary']};\n";
        if (!empty($dp['primary_hover'])) $css .= "  --primary-hover: {$dp['primary_hover']};\n";
        $css .= "}\n";

        return $css;
    }

    /**
     * Get cached published theme CSS.
     *
     * Defensive against stale cache (PHP deserialization of old objects returns
     * __PHP_Incomplete_Class). If the cache value is not a plain string, flush and
     * regenerate so the public layout never crashes.
     */
    public function getCachedThemeCss(): string
    {
        try {
            $cached = Cache::get(self::CACHE_KEY_PUBLISHED_CSS);
            if ($cached !== null) {
                if (!is_string($cached)) {
                    throw new \RuntimeException('Cached theme CSS is not a string — flushing.');
                }
                return $cached;
            }
        } catch (\Throwable $e) {
            Cache::forget(self::CACHE_KEY_PUBLISHED_CSS);
        }

        try {
            $settings = $this->getPublishedSettings();
            $css = $this->generateCssVariables($settings);
        } catch (\Throwable $e) {
            // If theme DB is unavailable, return empty string rather than 500
            $css = '';
        }

        try {
            Cache::put(self::CACHE_KEY_PUBLISHED_CSS, $css, 86400);
        } catch (\Throwable $e) {
            // Cache write failure must never crash public pages
        }

        return $css;
    }

    /**
     * Invalidate theme cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_PUBLISHED_CSS);
        Cache::forget(self::CACHE_KEY_PUBLISHED_SETTINGS);
    }

    /**
     * Normalize model or array settings into complete schema.
     */
    protected function normalizeSettings(ThemeSetting $setting): array
    {
        $defaults = $this->getDefaultSettings();

        return [
            'branding' => array_merge($defaults['branding'], $setting->branding ?? []),
            'global_colors' => array_merge($defaults['global_colors'], $setting->global_colors ?? []),
            'light_palette' => array_merge($defaults['light_palette'], $setting->light_palette ?? []),
            'dark_palette' => array_merge($defaults['dark_palette'], $setting->dark_palette ?? []),
            'typography' => array_merge($defaults['typography'], $setting->typography ?? []),
            'layout' => array_merge($defaults['layout'], $setting->layout ?? []),
            'glassmorphism' => array_merge($defaults['glassmorphism'], $setting->glassmorphism ?? []),
            'buttons' => array_merge($defaults['buttons'], $setting->buttons ?? []),
            'animations' => array_merge($defaults['animations'], $setting->animations ?? []),
        ];
    }
}
