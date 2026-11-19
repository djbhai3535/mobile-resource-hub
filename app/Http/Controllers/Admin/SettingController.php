<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display WhatsApp support settings form.
     */
    public function whatsapp(): View
    {
        $enabled = (bool) SiteSetting::get('whatsapp_enabled', false);
        $showHeader = (bool) SiteSetting::get('whatsapp_show_header', true);
        $number = SiteSetting::get('whatsapp_number', '');
        $message = SiteSetting::get('whatsapp_default_message', 'Hello, I need help with Mobile Resource Hub.');
        $previewUrl = SiteSetting::getWhatsAppUrl();

        return view('admin.settings.whatsapp', compact('enabled', 'showHeader', 'number', 'message', 'previewUrl'));
    }

    /**
     * Update WhatsApp support settings.
     */
    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_enabled' => 'nullable|boolean',
            'whatsapp_show_header' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string|max:30',
            'whatsapp_default_message' => 'nullable|string|max:500',
        ]);

        $enabled = $request->boolean('whatsapp_enabled');
        $showHeader = $request->boolean('whatsapp_show_header');
        $number = trim($validated['whatsapp_number'] ?? '');
        $message = trim($validated['whatsapp_default_message'] ?? 'Hello, I need help with Mobile Resource Hub.');

        if ($enabled && empty(preg_replace('/[^0-9]/', '', $number))) {
            return back()->withInput()->with('error', 'Please enter a valid international WhatsApp phone number (with country code, digits only) to enable support.');
        }

        $setting = SiteSetting::set('whatsapp_enabled', $enabled, 'boolean', 'whatsapp', 'WhatsApp Support Enabled');
        SiteSetting::set('whatsapp_show_header', $showHeader, 'boolean', 'whatsapp', 'WhatsApp Header Link Enabled');
        SiteSetting::set('whatsapp_number', $number, 'string', 'whatsapp', 'WhatsApp Phone Number');
        SiteSetting::set('whatsapp_default_message', $message, 'string', 'whatsapp', 'WhatsApp Default Message');

        ActivityLogger::log('settings_change', $setting, [
            'description' => 'Updated WhatsApp support settings: ' . ($enabled ? 'Enabled (' . $number . ')' : 'Disabled'),
            'enabled' => $enabled,
            'show_header' => $showHeader,
            'number' => $number,
        ]);

        return redirect()->route('admin.settings.whatsapp')->with('success', 'WhatsApp support button settings updated successfully!');
    }

    /**
     * Display Website Logo, Favicon & Branding settings form.
     */
    public function branding(): View
    {
        $siteName = SiteSetting::getSiteName();
        $siteLogo = SiteSetting::get('site_logo');
        $siteLogoDark = SiteSetting::get('site_logo_dark');
        $siteFavicon = SiteSetting::get('site_favicon');

        $siteLogoUrl = SiteSetting::getLogoUrl();
        $siteLogoDarkUrl = SiteSetting::getLogoDarkUrl();
        $siteFaviconUrl = SiteSetting::getFaviconUrl();

        $logoWidth = SiteSetting::getLogoWidth();
        $logoHeight = SiteSetting::getLogoHeight();

        return view('admin.settings.branding', compact(
            'siteName',
            'siteLogo',
            'siteLogoDark',
            'siteFavicon',
            'siteLogoUrl',
            'siteLogoDarkUrl',
            'siteFaviconUrl',
            'logoWidth',
            'logoHeight'
        ));
    }

    /**
     * Update Website Logo, Favicon & Branding settings.
     */
    public function updateBranding(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:100',
            'site_logo' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:4096',
            'site_logo_dark' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:4096',
            'site_favicon' => 'nullable|file|mimes:ico,png,svg|max:2048',
            'site_logo_width' => 'nullable|integer|min:20|max:400',
            'site_logo_height' => 'nullable|integer|min:16|max:120',
            'remove_logo' => 'nullable|boolean',
            'remove_logo_dark' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
        ]);

        // 1. Site Name
        if ($request->filled('site_name')) {
            SiteSetting::set('site_name', trim($validated['site_name']), 'string', 'branding', 'Website Name');
        }

        // 2. Logo Dimensions
        $width = $request->input('site_logo_width') ? (int) $request->input('site_logo_width') : 140;
        $height = $request->input('site_logo_height') ? (int) $request->input('site_logo_height') : 36;
        SiteSetting::set('site_logo_width', $width, 'integer', 'branding', 'Logo Max Width (px)');
        SiteSetting::set('site_logo_height', $height, 'integer', 'branding', 'Logo Max Height (px)');

        // 3. Remove Primary Logo
        if ($request->boolean('remove_logo')) {
            $existing = SiteSetting::get('site_logo');
            if ($existing && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }
            SiteSetting::set('site_logo', null, 'string', 'branding', 'Website Logo Path');
        } elseif ($request->hasFile('site_logo')) {
            $existing = SiteSetting::get('site_logo');
            if ($existing && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }
            $file = $request->file('site_logo');
            $fileName = 'logo_' . time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('branding', $fileName, 'public');
            SiteSetting::set('site_logo', $path, 'string', 'branding', 'Website Logo Path');
        }

        // 4. Remove Dark Variant Logo
        if ($request->boolean('remove_logo_dark')) {
            $existingDark = SiteSetting::get('site_logo_dark');
            if ($existingDark && Storage::disk('public')->exists($existingDark)) {
                Storage::disk('public')->delete($existingDark);
            }
            SiteSetting::set('site_logo_dark', null, 'string', 'branding', 'Website Dark Logo Path');
        } elseif ($request->hasFile('site_logo_dark')) {
            $existingDark = SiteSetting::get('site_logo_dark');
            if ($existingDark && Storage::disk('public')->exists($existingDark)) {
                Storage::disk('public')->delete($existingDark);
            }
            $file = $request->file('site_logo_dark');
            $fileName = 'logo_dark_' . time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('branding', $fileName, 'public');
            SiteSetting::set('site_logo_dark', $path, 'string', 'branding', 'Website Dark Logo Path');
        }

        // 5. Remove Favicon
        if ($request->boolean('remove_favicon')) {
            $existingFavicon = SiteSetting::get('site_favicon');
            if ($existingFavicon && Storage::disk('public')->exists($existingFavicon)) {
                Storage::disk('public')->delete($existingFavicon);
            }
            SiteSetting::set('site_favicon', null, 'string', 'branding', 'Browser Favicon Path');
        } elseif ($request->hasFile('site_favicon')) {
            $existingFavicon = SiteSetting::get('site_favicon');
            if ($existingFavicon && Storage::disk('public')->exists($existingFavicon)) {
                Storage::disk('public')->delete($existingFavicon);
            }
            $file = $request->file('site_favicon');
            $fileName = 'favicon_' . time() . '_' . Str::random(8) . '.' . strtolower($file->getClientOriginalExtension());
            $path = $file->storeAs('branding', $fileName, 'public');
            SiteSetting::set('site_favicon', $path, 'string', 'branding', 'Browser Favicon Path');
        }

        ActivityLogger::log('settings_change', null, [
            'description' => 'Updated website logo, favicon & branding configuration',
            'site_name' => SiteSetting::getSiteName(),
        ]);

        return redirect()->route('admin.settings.branding')->with('success', 'Website logo, favicon, and branding settings updated successfully!');
    }
}
