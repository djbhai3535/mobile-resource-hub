<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdClickController extends Controller
{
    /**
     * Track advertisement click and redirect securely.
     */
    public function trackClick(Request $request, int $id, AdvertisementService $adService): RedirectResponse
    {
        $ad = Advertisement::findOrFail($id);

        // Detect device category from User Agent
        $userAgent = strtolower($request->userAgent() ?? '');
        $device = 'desktop';
        if (str_contains($userAgent, 'mobile')) {
            $device = 'mobile';
        } elseif (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            $device = 'tablet';
        }

        // Record genuine click
        $adService->recordClick($ad, $device);

        $destination = $ad->destination_url;

        // Open redirect prevention: validate destination URL
        if (empty($destination) || !$adService->isValidDestinationUrl($destination)) {
            return redirect('/');
        }

        return redirect()->away($destination);
    }
}
