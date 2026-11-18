<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Display WhatsApp support settings form.
     */
    public function whatsapp(): View
    {
        $enabled = (bool) SiteSetting::get('whatsapp_enabled', false);
        $number = SiteSetting::get('whatsapp_number', '');
        $message = SiteSetting::get('whatsapp_default_message', 'Hello, I need help with Mobile Resource Hub.');
        $previewUrl = SiteSetting::getWhatsAppUrl();

        return view('admin.settings.whatsapp', compact('enabled', 'number', 'message', 'previewUrl'));
    }

    /**
     * Update WhatsApp support settings.
     */
    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_enabled' => 'nullable|boolean',
            'whatsapp_number' => 'nullable|string|max:30',
            'whatsapp_default_message' => 'nullable|string|max:500',
        ]);

        $enabled = $request->boolean('whatsapp_enabled');
        $number = trim($validated['whatsapp_number'] ?? '');
        $message = trim($validated['whatsapp_default_message'] ?? 'Hello, I need help with Mobile Resource Hub.');

        if ($enabled && empty(preg_replace('/[^0-9]/', '', $number))) {
            return back()->withInput()->with('error', 'Please enter a valid international WhatsApp phone number (with country code, digits only) to enable support.');
        }

        $setting = SiteSetting::set('whatsapp_enabled', $enabled, 'boolean', 'whatsapp', 'WhatsApp Support Enabled');
        SiteSetting::set('whatsapp_number', $number, 'string', 'whatsapp', 'WhatsApp Phone Number');
        SiteSetting::set('whatsapp_default_message', $message, 'string', 'whatsapp', 'WhatsApp Default Message');

        ActivityLogger::log('settings_change', $setting, [
            'description' => 'Updated WhatsApp support settings: ' . ($enabled ? 'Enabled (' . $number . ')' : 'Disabled'),
            'enabled' => $enabled,
            'number' => $number,
        ]);

        return redirect()->route('admin.settings.whatsapp')->with('success', 'WhatsApp support button settings updated successfully!');
    }
}
