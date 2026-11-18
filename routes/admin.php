<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\FirmwareController;
use App\Http\Controllers\Admin\FlashFileController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SearchAnalyticsController;
use App\Http\Controllers\Admin\SoftwareController;
use App\Http\Controllers\Admin\SpecificationController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\ThemeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile Resource Hub — Administration Portal Routes
|--------------------------------------------------------------------------
| All administrative routes run under the /admin prefix and use the 'admin.'
| named route convention. Protected routes require the 'admin.auth' middleware.
|
*/

// Authentication (Guest Admin)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/password/reset', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

// Authenticated Admin Area
Route::middleware('admin.auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('root');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mobile Brands Management
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/{id}/restore', [BrandController::class, 'restore'])->name('brands.restore');

    // Mobile Devices Management
    Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->name('devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->name('devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->name('devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::post('/devices/{id}/restore', [DeviceController::class, 'restore'])->name('devices.restore');

    // Dynamic Specification Builder
    Route::get('/specifications', [SpecificationController::class, 'index'])->name('specifications.index');
    Route::post('/specifications/groups', [SpecificationController::class, 'storeGroup'])->name('specifications.groups.store');
    Route::put('/specifications/groups/{group}', [SpecificationController::class, 'updateGroup'])->name('specifications.groups.update');
    Route::delete('/specifications/groups/{group}', [SpecificationController::class, 'destroyGroup'])->name('specifications.groups.destroy');
    Route::post('/specifications/fields', [SpecificationController::class, 'storeField'])->name('specifications.fields.store');
    Route::put('/specifications/fields/{field}', [SpecificationController::class, 'updateField'])->name('specifications.fields.update');
    Route::delete('/specifications/fields/{field}', [SpecificationController::class, 'destroyField'])->name('specifications.fields.destroy');

    // Firmware Releases Management
    Route::get('/firmware', [FirmwareController::class, 'index'])->name('firmware.index');
    Route::get('/firmware/create', [FirmwareController::class, 'create'])->name('firmware.create');
    Route::post('/firmware', [FirmwareController::class, 'store'])->name('firmware.store');
    Route::get('/firmware/{firmware}/edit', [FirmwareController::class, 'edit'])->name('firmware.edit');
    Route::put('/firmware/{firmware}', [FirmwareController::class, 'update'])->name('firmware.update');
    Route::delete('/firmware/{firmware}', [FirmwareController::class, 'destroy'])->name('firmware.destroy');
    Route::post('/firmware/{id}/restore', [FirmwareController::class, 'restore'])->name('firmware.restore');

    // Flash Files Management
    Route::get('/flash-files', [FlashFileController::class, 'index'])->name('flash-files.index');
    Route::get('/flash-files/create', [FlashFileController::class, 'create'])->name('flash-files.create');
    Route::post('/flash-files', [FlashFileController::class, 'store'])->name('flash-files.store');
    Route::get('/flash-files/{flashFile}/edit', [FlashFileController::class, 'edit'])->name('flash-files.edit');
    Route::put('/flash-files/{flashFile}', [FlashFileController::class, 'update'])->name('flash-files.update');
    Route::delete('/flash-files/{flashFile}', [FlashFileController::class, 'destroy'])->name('flash-files.destroy');
    Route::post('/flash-files/{id}/restore', [FlashFileController::class, 'restore'])->name('flash-files.restore');

    // Software Tools Management
    Route::get('/software', [SoftwareController::class, 'index'])->name('software.index');
    Route::get('/software/create', [SoftwareController::class, 'create'])->name('software.create');
    Route::post('/software', [SoftwareController::class, 'store'])->name('software.store');
    Route::get('/software/{software}/edit', [SoftwareController::class, 'edit'])->name('software.edit');
    Route::put('/software/{software}', [SoftwareController::class, 'update'])->name('software.update');
    Route::delete('/software/{software}', [SoftwareController::class, 'destroy'])->name('software.destroy');
    Route::post('/software/{id}/restore', [SoftwareController::class, 'restore'])->name('software.restore');

    // USB Drivers Management
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create');
    Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');
    Route::post('/drivers/{id}/restore', [DriverController::class, 'restore'])->name('drivers.restore');

    // Posts & Editorial CRUD
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::post('/posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');
    Route::post('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');

    // Taxonomy (Categories & Tags)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    // Static Pages CMS
    Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');

    // Media Library
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::put('/media/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Contact Messages
    Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/toggle-read', [ContactMessageController::class, 'toggleRead'])->name('messages.toggle-read');
    Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    // Navigation Menus
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
    Route::post('/menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::put('/menu-items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
    Route::delete('/menu-items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');

    // Search Analytics & Index Management
    Route::get('/search-analytics', [SearchAnalyticsController::class, 'index'])->name('search-analytics.index');
    Route::post('/search-analytics/rebuild', [SearchAnalyticsController::class, 'rebuild'])->name('search-analytics.rebuild');

    // Theme Customizer & Design Studio
    Route::get('/theme', [ThemeController::class, 'index'])->name('theme.index');
    Route::get('/theme/preview', [ThemeController::class, 'preview'])->name('theme.preview');
    Route::post('/theme/draft', [ThemeController::class, 'saveDraft'])->name('theme.draft');
    Route::post('/theme/publish', [ThemeController::class, 'publish'])->name('theme.publish');
    Route::post('/theme/reset', [ThemeController::class, 'resetDraft'])->name('theme.reset');
    Route::get('/theme/history', [ThemeController::class, 'history'])->name('theme.history');
    Route::post('/theme/restore/{id}', [ThemeController::class, 'restore'])->name('theme.restore');

    // Homepage Section Layout Manager
    Route::get('/homepage', [HomepageSectionController::class, 'index'])->name('homepage.index');
    Route::put('/homepage/sections/{id}', [HomepageSectionController::class, 'update'])->name('homepage.update');
    Route::post('/homepage/sections/{id}/toggle', [HomepageSectionController::class, 'toggle'])->name('homepage.toggle');
    Route::post('/homepage/reorder', [HomepageSectionController::class, 'reorder'])->name('homepage.reorder');

    // Advertisement Manager & Campaigns
    Route::get('/advertisements', [AdvertisementController::class, 'index'])->name('advertisements.index');
    Route::get('/advertisements/create', [AdvertisementController::class, 'create'])->name('advertisements.create');
    Route::post('/advertisements', [AdvertisementController::class, 'store'])->name('advertisements.store');
    Route::get('/advertisements/placements', [AdvertisementController::class, 'placements'])->name('advertisements.placements');
    Route::get('/advertisements/analytics', [AdvertisementController::class, 'analytics'])->name('advertisements.analytics');
    Route::get('/advertisements/{id}/edit', [AdvertisementController::class, 'edit'])->name('advertisements.edit');
    Route::put('/advertisements/{id}', [AdvertisementController::class, 'update'])->name('advertisements.update');
    Route::delete('/advertisements/{id}', [AdvertisementController::class, 'destroy'])->name('advertisements.destroy');

    // Audit & Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.index');

    // Platform & Support Settings
    Route::get('/settings/whatsapp', [\App\Http\Controllers\Admin\SettingController::class, 'whatsapp'])->name('settings.whatsapp');
    Route::post('/settings/whatsapp', [\App\Http\Controllers\Admin\SettingController::class, 'updateWhatsapp'])->name('settings.whatsapp.update');
});
