<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    public const CACHE_PREFIX = 'mrh_setting_';

    /**
     * Get a setting by key with fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(self::CACHE_PREFIX . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true) ?? $default,
                default => $setting->value ?? $default,
            };
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general', ?string $label = null): static
    {
        $storedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => is_array($value) ? json_encode($value) : $value,
            default => $value === null ? null : (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'type' => $type,
                'group' => $group,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );

        Cache::forget(self::CACHE_PREFIX . $key);

        return $setting;
    }

    /**
     * Clean sanitized WhatsApp Click-to-chat URL.
     */
    public static function getWhatsAppUrl(): ?string
    {
        $enabled = static::get('whatsapp_enabled', false);
        if (!$enabled) {
            return null;
        }

        $phone = static::get('whatsapp_number', '');
        // Strip everything except digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($cleanPhone)) {
            return null;
        }

        $message = static::get('whatsapp_default_message', 'Hello, I need help with Mobile Resource Hub.');
        return 'https://wa.me/' . $cleanPhone . '?text=' . urlencode($message);
    }

    /**
     * Check if compact WhatsApp button should be rendered in top navigation.
     */
    public static function isWhatsAppHeaderEnabled(): bool
    {
        return (bool) static::get('whatsapp_show_header', true);
    }

    /**
     * Get site name with fallback to app.name.
     */
    public static function getSiteName(): string
    {
        return (string) static::get('site_name', config('app.name', 'Mobile Resource Hub'));
    }

    /**
     * Get public URL for website logo (supports light/dark variants).
     */
    public static function getLogoUrl(?string $mode = null): ?string
    {
        if ($mode === 'dark') {
            $darkLogo = static::get('site_logo_dark');
            if ($darkLogo) {
                return self::formatStorageUrl($darkLogo);
            }
        }

        $logo = static::get('site_logo');
        if (!$logo) {
            return null;
        }

        return self::formatStorageUrl($logo);
    }

    /**
     * Get public URL for dark variant website logo.
     */
    public static function getLogoDarkUrl(): ?string
    {
        return static::getLogoUrl('dark');
    }

    /**
     * Get public URL for browser favicon.
     */
    public static function getFaviconUrl(): string
    {
        $favicon = static::get('site_favicon');
        if ($favicon) {
            return self::formatStorageUrl($favicon);
        }

        return asset('favicon.ico');
    }

    /**
     * Configured logo display max width in pixels.
     */
    public static function getLogoWidth(): int
    {
        $w = (int) static::get('site_logo_width', 140);
        return ($w >= 20 && $w <= 400) ? $w : 140;
    }

    /**
     * Configured logo display max height in pixels.
     */
    public static function getLogoHeight(): int
    {
        $h = (int) static::get('site_logo_height', 36);
        return ($h >= 16 && $h <= 120) ? $h : 36;
    }

    /**
     * Internal helper to format local storage or external absolute URLs.
     */
    protected static function formatStorageUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
