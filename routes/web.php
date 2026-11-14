<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\PublicPageController;
use App\Http\Controllers\Public\DownloadController;

/*
|--------------------------------------------------------------------------
| Mobile Resource Hub — Public Web Routes
|--------------------------------------------------------------------------
|
| PRD Section 5: Information Architecture & Public Routes
| All public routes are server-rendered, crawlable, and protected against
| duplicate canonical index pollution.
|
*/

// Homepage
Route::get('/', [PublicPageController::class, 'home'])->name('home');

// Devices & Brands
Route::get('/mobile-phones', [PublicPageController::class, 'devices'])->name('devices.index');
Route::get('/mobile-phones/{device}', [PublicPageController::class, 'deviceDetail'])->name('public.device.detail');
Route::get('/brands', [PublicPageController::class, 'brands'])->name('brands.index');
Route::get('/brands/{brand}', [PublicPageController::class, 'brandDetail'])->name('public.brand.detail');

// Firmware & Flash Files
Route::get('/firmware', [PublicPageController::class, 'firmware'])->name('firmware.index');
Route::get('/firmware/{firmware}', [PublicPageController::class, 'firmwareDetail'])->name('firmware.show');
Route::get('/flash-files', [PublicPageController::class, 'flashFiles'])->name('flash-files.index');
Route::get('/flash-files/{file}', [PublicPageController::class, 'flashFileDetail'])->name('flash-files.show');

// Download Gateway (Handles local streaming & external redirect tracking)
Route::get('/downloads/mirror/{mirror}', [DownloadController::class, 'downloadMirror'])->name('downloads.mirror')->middleware('throttle:60,1');

// Software Tools & USB Drivers
Route::get('/software', [PublicPageController::class, 'software'])->name('software.index');
Route::get('/software/{software}', [PublicPageController::class, 'softwareDetail'])->name('software.show');
Route::get('/drivers', [PublicPageController::class, 'drivers'])->name('drivers.index');
Route::get('/drivers/{driver}', [PublicPageController::class, 'driverDetail'])->name('drivers.show');

// Editorial (News & Tutorials)
Route::get('/news', [PublicPageController::class, 'news'])->name('news.index');
Route::get('/news/{article}', [PublicPageController::class, 'newsDetail'])->name('news.show');
Route::get('/tutorials', [PublicPageController::class, 'tutorials'])->name('tutorials.index');
Route::get('/tutorials/{tutorial}', [PublicPageController::class, 'tutorialDetail'])->name('tutorials.show');

// Global Search & Live Suggestions API
Route::get('/search', [PublicPageController::class, 'search'])->name('search');
Route::get('/api/search/suggestions', [PublicPageController::class, 'searchSuggestions'])->name('search.suggestions');

// Static & Legal Pages
Route::get('/about-us', [PublicPageController::class, 'aboutUs'])->name('about');
Route::get('/contact-us', [PublicPageController::class, 'contactUs'])->name('contact');
Route::post('/contact-us', function (\Illuminate\Http\Request $request) {
    // Validate contact submission
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email|max:150',
        'subject' => 'required|string|max:200',
        'message' => 'required|string|max:3000',
    ]);

    // Honeypot check: spambot filled hidden field
    if ($request->filled('mrh_website_hp')) {
        return back()->with('success', 'Thank you! Your message has been received.');
    }

    // Persist genuine inquiry into database
    \App\Models\ContactMessage::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'subject' => $validated['subject'],
        'message' => $validated['message'],
        'status' => 'unread',
        'ip_hash' => hash('sha256', $request->ip() . '_mrh_salt'),
    ]);

    return back()->with('success', 'Thank you! Your message has been received. Our team will review it shortly.');
})->name('contact.submit');

Route::get('/privacy-policy', [PublicPageController::class, 'privacyPolicy'])->name('privacy');
Route::get('/terms-and-conditions', [PublicPageController::class, 'termsAndConditions'])->name('terms');
Route::get('/dmca', [PublicPageController::class, 'dmca'])->name('dmca');

// Technical SEO & XML Sitemap Engine (PRD Section 10)
Route::get('/sitemap.xml', [\App\Http\Controllers\Public\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemaps/{section}.xml', [\App\Http\Controllers\Public\SitemapController::class, 'subSitemap'])->name('sitemap.section');
Route::get('/robots.txt', [\App\Http\Controllers\Public\SitemapController::class, 'robots'])->name('robots.txt');

// Advertisement Click Tracking Gateway
Route::get('/ad/click/{ad}', [\App\Http\Controllers\Public\AdClickController::class, 'trackClick'])->name('ad.click')->middleware('throttle:60,1');
