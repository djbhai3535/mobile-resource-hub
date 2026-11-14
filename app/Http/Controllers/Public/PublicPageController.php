<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Device;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\Post;
use App\Models\SoftwareTool;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PublicPageController extends Controller
{
    /**
     * Display the Public Homepage.
     */
    public function home(\App\Services\HomepageService $homepageService): View
    {
        $sections = $homepageService->getOrderedSections(false)->keyBy('section_key');
        try {
            $dbBrands = Brand::published()->withCount('devices')->orderBy('sort_order')->orderBy('name')->take(6)->get();
        } catch (\Throwable $e) {
            $dbBrands = collect([]);
        }

        if ($dbBrands->isNotEmpty()) {
            $popularBrands = $dbBrands->map(function ($b) {
                return [
                    'name' => $b->name,
                    'slug' => $b->slug,
                    'count' => $b->devices_count,
                    'icon' => strtolower($b->slug) === 'apple' ? 'bi-apple' : (strtolower($b->slug) === 'google' ? 'bi-google' : 'bi-phone'),
                    'logo' => $b->logo_url,
                ];
            })->toArray();
        } else {
            $popularBrands = [
                ['name' => 'Samsung', 'slug' => 'samsung', 'count' => 142, 'icon' => 'bi-phone', 'logo' => null],
                ['name' => 'Apple', 'slug' => 'apple', 'count' => 58, 'icon' => 'bi-apple', 'logo' => null],
                ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'count' => 110, 'icon' => 'bi-phone', 'logo' => null],
                ['name' => 'Google', 'slug' => 'google', 'count' => 34, 'icon' => 'bi-google', 'logo' => null],
                ['name' => 'OnePlus', 'slug' => 'oneplus', 'count' => 45, 'icon' => 'bi-phone', 'logo' => null],
                ['name' => 'Motorola', 'slug' => 'motorola', 'count' => 62, 'icon' => 'bi-phone', 'logo' => null],
            ];
        }

        try {
            $dbDevices = Device::published()->with(['brand', 'modelNumbers', 'specificationValues.field'])->latest('release_date')->latest('id')->take(4)->get();
        } catch (\Throwable $e) {
            $dbDevices = collect([]);
        }
        if ($dbDevices->isNotEmpty()) {
            $latestDevices = $dbDevices->map(function ($d) {
                $specs = $d->specificationValues->keyBy(fn($v) => $v->field?->key ?? '');
                return [
                    'name' => $d->name,
                    'brand' => $d->brand->name ?? 'Unknown',
                    'model' => $d->modelNumbers->first()?->model_number ?? 'Global',
                    'slug' => $d->slug,
                    'screen' => $specs['display_size']->value ?? ($d->short_description ?? 'Super AMOLED'),
                    'chipset' => $specs['platform_chipset']->value ?? ($d->os ?? 'High-End SoC'),
                    'camera' => $specs['main_camera_setup']->value ?? 'Pro Camera Array',
                    'battery' => $specs['battery_capacity']->value ?? 'High-Capacity Battery',
                    'release_year' => $d->release_date ? $d->release_date->format('Y') : '2024',
                    'verified' => true,
                ];
            })->toArray();
        } else {
            $latestDevices = [
                [
                    'name' => 'Galaxy S24 Ultra',
                    'brand' => 'Samsung',
                    'model' => 'SM-S928B',
                    'slug' => 'samsung-galaxy-s24-ultra',
                    'screen' => '6.8" Dynamic AMOLED 2X 120Hz',
                    'chipset' => 'Snapdragon 8 Gen 3 for Galaxy',
                    'camera' => '200 MP Quad Camera',
                    'battery' => '5000 mAh (45W)',
                    'release_year' => '2024',
                    'verified' => true,
                ],
            ];
        }

        $recentFirmware = [
            [
                'title' => 'Galaxy S24 Ultra Android 15 OneUI 7.0 (Global)',
                'slug' => 'galaxy-s24-ultra-sm-s928b-oneui7',
                'model_code' => 'SM-S928B',
                'region' => 'OXM (Multi-CSC)',
                'build' => 'S928BXXU3BXK2',
                'size' => '9.2 GB',
                'date' => 'Nov 2026',
            ],
            [
                'title' => 'Redmi Note 13 Pro 5G HyperOS 2.0 Global Stable',
                'slug' => 'redmi-note-13-pro-5g-hyperos2-global',
                'model_code' => 'garnet',
                'region' => 'Global (MI)',
                'build' => 'OS2.0.1.0.UNRMIXM',
                'size' => '5.6 GB',
                'date' => 'Nov 2026',
            ],
            [
                'title' => 'Pixel 9 Pro Factory Image Android 15 Stable',
                'slug' => 'pixel-9-pro-factory-image-android15',
                'model_code' => 'caiman',
                'region' => 'Global',
                'build' => 'AP4A.241105.008',
                'size' => '3.8 GB',
                'date' => 'Nov 2026',
            ],
        ];

        $latestGuides = [
            [
                'title' => 'How to Flash Official Samsung Firmware Using Odin 3.14',
                'slug' => 'how-to-flash-samsung-firmware-using-odin',
                'category' => 'Tutorials',
                'date' => '10 Nov 2026',
                'read_time' => '6 min read',
            ],
            [
                'title' => 'Fix Qualcomm HS-USB QDLoader 9008 Mode Brick Step-by-Step',
                'slug' => 'fix-qualcomm-hs-usb-qdloader-9008-edl-brick',
                'category' => 'Repair Guides',
                'date' => '08 Nov 2026',
                'read_time' => '9 min read',
            ],
        ];

        return view('public.home', compact('popularBrands', 'latestDevices', 'recentFirmware', 'latestGuides', 'sections'));
    }

    /**
     * All Mobile Phones Directory.
     */
    public function devices(Request $request): View
    {
        $search = $request->query('search');
        $brandFilter = $request->query('brand');

        $query = Device::published()->with(['brand', 'modelNumbers', 'specificationValues.field']);

        if (!empty($search)) {
            $keyword = '%' . $search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('marketing_name', 'like', $keyword)
                    ->orWhere('slug', 'like', $keyword)
                    ->orWhereHas('modelNumbers', function ($mq) use ($keyword) {
                        $mq->where('model_number', 'like', $keyword);
                    });
            });
        }

        if (!empty($brandFilter)) {
            $query->whereHas('brand', function ($bq) use ($brandFilter) {
                $bq->where('slug', $brandFilter);
            });
        }

        $dbDevices = $query->latest('release_date')->latest('id')->paginate(18)->withQueryString();

        if ($dbDevices->isNotEmpty()) {
            $devices = $dbDevices->map(function ($d) {
                $specs = $d->specificationValues->keyBy(fn($v) => $v->field?->key ?? '');
                return [
                    'name' => $d->name,
                    'brand' => $d->brand->name ?? 'Unknown',
                    'model' => $d->modelNumbers->first()?->model_number ?? 'Global',
                    'slug' => $d->slug,
                    'screen' => $specs['display_size']->value ?? ($d->short_description ?? 'Full HD+ Display'),
                    'chipset' => $specs['platform_chipset']->value ?? ($d->os ?? 'High-End SoC'),
                    'camera' => $specs['main_camera_setup']->value ?? 'Pro Camera Array',
                    'battery' => $specs['battery_capacity']->value ?? 'Lithium Battery',
                    'release_year' => $d->release_date ? $d->release_date->format('Y') : '2024',
                    'os' => $d->os ? ($d->os . ($d->os_version ? ', ' . $d->os_version : '')) : 'Android',
                    'featured_image' => $d->featured_image,
                    'device_category' => $d->device_category,
                ];
            });
        } elseif (empty($search) && empty($brandFilter)) {
            // Seamless fallback for unseeded or demo environments
            $devices = collect([
                [
                    'name' => 'Galaxy S24 Ultra',
                    'brand' => 'Samsung',
                    'model' => 'SM-S928B',
                    'slug' => 'galaxy-s24-ultra',
                    'screen' => '6.8" Dynamic AMOLED 2X 120Hz',
                    'chipset' => 'Snapdragon 8 Gen 3 for Galaxy',
                    'camera' => '200 MP Quad Camera',
                    'battery' => '5000 mAh (45W)',
                    'release_year' => '2024',
                    'os' => 'Android 14',
                    'featured_image' => null,
                    'device_category' => 'smartphone',
                ],
                [
                    'name' => 'iPhone 16 Pro Max',
                    'brand' => 'Apple',
                    'model' => 'A3296',
                    'slug' => 'iphone-16-pro-max',
                    'screen' => '6.9" Super Retina XDR OLED',
                    'chipset' => 'Apple A18 Pro (3 nm)',
                    'camera' => '48 MP Fusion + 5x Telephoto',
                    'battery' => '4685 mAh',
                    'release_year' => '2024',
                    'os' => 'iOS 18',
                    'featured_image' => null,
                    'device_category' => 'smartphone',
                ],
            ]);
        } else {
            $devices = collect([]);
        }

        $allBrands = Brand::published()->orderBy('name')->get();

        return view('public.devices', compact('devices', 'search', 'brandFilter', 'dbDevices', 'allBrands'));
    }

    /**
     * Brand Directory.
     */
    public function brands(): View
    {
        $dbBrands = Brand::published()->withCount('devices')->orderBy('sort_order')->orderBy('name')->get();

        if ($dbBrands->isNotEmpty()) {
            $brands = $dbBrands->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'slug' => $b->slug,
                    'count' => $b->devices_count,
                    'origin' => $b->country_of_origin ?: 'Global',
                    'founded' => 'Verified Manufacturer',
                    'logo' => $b->logo_url,
                    'short_description' => $b->short_description,
                ];
            })->toArray();
        } else {
            $brands = [
                ['name' => 'Samsung', 'slug' => 'samsung', 'count' => 142, 'origin' => 'South Korea', 'founded' => 1938, 'logo' => null, 'short_description' => 'Global electronics leader.'],
                ['name' => 'Apple', 'slug' => 'apple', 'count' => 58, 'origin' => 'United States', 'founded' => 1976, 'logo' => null, 'short_description' => 'Creator of iPhone and iOS.'],
                ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'count' => 110, 'origin' => 'China', 'founded' => 2010, 'logo' => null, 'short_description' => 'Hyper-charge and Leica mobile imaging.'],
            ];
        }

        return view('public.brands', compact('brands', 'dbBrands'));
    }

    /**
     * Individual Brand Detail Page.
     */
    public function brandDetail(string $brandSlug): View
    {
        $brand = Brand::published()->where('slug', $brandSlug)->withCount('devices')->first();

        if ($brand) {
            $brandName = $brand->name;
            $devices = $brand->devices()->published()->with(['modelNumbers', 'variants'])->latest('release_date')->paginate(12);
        } else {
            $brandName = ucfirst($brandSlug);
            $devices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 12);
        }

        return view('public.brand-detail', compact('brand', 'brandSlug', 'brandName', 'devices'));
    }

    /**
     * Device Specification Detail Page.
     */
    public function deviceDetail(string $deviceSlug): View
    {
        $deviceModel = Device::published()->where('slug', $deviceSlug)
            ->with(['brand', 'modelNumbers', 'variants', 'specificationValues.field.group'])
            ->first();

        if ($deviceModel) {
            $groupedSpecs = $deviceModel->getGroupedSpecifications();
            $specsByKey = $deviceModel->specificationValues->keyBy(fn($v) => $v->field?->key ?? '');

            $device = [
                'name' => $deviceModel->marketing_name ?: ($deviceModel->brand->name . ' ' . $deviceModel->name),
                'brand' => $deviceModel->brand->name ?? 'Unknown',
                'model' => $deviceModel->modelNumbers->pluck('model_number')->implode(' / ') ?: 'Standard',
                'slug' => $deviceModel->slug,
                'announced' => $specsByKey['launch_announced']->value ?? ($deviceModel->announced_date ? $deviceModel->announced_date->format('F d, Y') : 'Announced'),
                'status' => $specsByKey['launch_market_status']->value ?? ucfirst($deviceModel->market_status),
                'dimensions' => $specsByKey['body_dimensions']->value ?? 'Standard Form Factor',
                'weight' => $specsByKey['body_weight']->value ?? 'Unspecified',
                'build' => $specsByKey['body_build']->value ?? 'Glass / Aluminum Composite',
                'ip_rating' => $specsByKey['body_resistance']->value ?? 'Water & Dust Sealed',
                'display_type' => $specsByKey['display_type']->value ?? 'Touchscreen Display',
                'display_size' => $specsByKey['display_size']->value ?? 'OLED Multi-Touch',
                'resolution' => $specsByKey['display_resolution']->value ?? 'FHD+ High Resolution',
                'os' => $specsByKey['platform_os']->value ?? ($deviceModel->os . ' ' . $deviceModel->os_version),
                'chipset' => $specsByKey['platform_chipset']->value ?? 'Integrated Processor SoC',
                'cpu' => $specsByKey['platform_cpu']->value ?? 'Multi-Core Processor',
                'gpu' => $specsByKey['platform_gpu']->value ?? 'Integrated Graphics',
                'internal_storage' => $specsByKey['memory_internal']->value ?? $deviceModel->variants->pluck('name')->implode(', '),
                'card_slot' => $specsByKey['memory_card_slot']->value ?? 'Nano-SIM / Card Tray',
                'main_camera' => $specsByKey['main_camera_modules']->value ?? ($specsByKey['main_camera_setup']->value ?? 'High-Resolution Camera Sensor'),
                'selfie_camera' => $specsByKey['selfie_camera_modules']->value ?? ($specsByKey['selfie_camera_setup']->value ?? 'Front Camera'),
                'battery' => $specsByKey['battery_capacity']->value ?? 'Rechargeable Battery',
                'colors' => $specsByKey['misc_colors']->value ?? 'Standard Finishes',
                'featured_image' => $deviceModel->featured_image,
                'image_gallery' => $deviceModel->image_gallery ?? [],
            ];

            $relatedDevices = Device::published()
                ->where('brand_id', $deviceModel->brand_id)
                ->where('id', '!=', $deviceModel->id)
                ->latest('release_date')
                ->take(3)
                ->get();

            $relatedFirmwares = $deviceModel->firmwares()->published()->latest('release_date')->take(5)->get();
            $relatedFlashFiles = $deviceModel->flashFiles()->published()->latest('id')->take(5)->get();
            $relatedDrivers = Driver::published()->where(function ($q) use ($deviceModel) {
                $q->where('brand_id', $deviceModel->brand_id)->orWhereNull('brand_id');
            })->take(4)->get();
        } else {
            $device = [
                'name' => 'Samsung Galaxy S24 Ultra',
                'brand' => 'Samsung',
                'model' => 'SM-S928B / SM-S928U',
                'slug' => $deviceSlug,
                'announced' => 'January 17, 2024',
                'status' => 'Available. Released January 24, 2024',
                'dimensions' => '162.3 x 79.0 x 8.6 mm (6.39 x 3.11 x 0.34 in)',
                'weight' => '232 g (8.18 oz)',
                'build' => 'Glass front (Corning Gorilla Armor), glass back, titanium frame (grade 2)',
                'ip_rating' => 'IP68 dust/water resistant (up to 1.5m for 30 min)',
                'display_type' => 'Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 2600 nits (peak)',
                'display_size' => '6.8 inches, 113.5 cm2 (~88.5% screen-to-body ratio)',
                'resolution' => '1440 x 3120 pixels, 19.5:9 ratio (~505 ppi density)',
                'os' => 'Android 14, upgradable to 7 major Android upgrades, One UI 7 ready',
                'chipset' => 'Qualcomm SM8650-AC Snapdragon 8 Gen 3 (4 nm) for Galaxy',
                'cpu' => '8-core (1x3.39GHz Cortex-X4 & 3x3.1GHz Cortex-A720 & 2x2.9GHz Cortex-A720 & 2x2.2GHz Cortex-A520)',
                'gpu' => 'Adreno 750 (1 GHz)',
                'internal_storage' => '256GB 12GB RAM, 512GB 12GB RAM, 1TB 12GB RAM (UFS 4.0)',
                'card_slot' => 'No',
                'main_camera' => '200 MP, f/1.7, 24mm (wide) + 50 MP, f/3.4, 111mm (periscope telephoto, 5x) + 10 MP, f/2.4, 67mm (telephoto, 3x) + 12 MP, f/2.2, 13mm (ultrawide)',
                'selfie_camera' => '12 MP, f/2.2, 26mm (wide), Dual Pixel PDAF',
                'battery' => '5000 mAh non-removable, 45W wired, 15W wireless, 4.5W reverse wireless',
                'colors' => 'Titanium Black, Titanium Gray, Titanium Violet, Titanium Yellow',
                'featured_image' => null,
                'image_gallery' => [],
            ];
            $groupedSpecs = [];
            $relatedDevices = collect([]);
            $relatedFirmwares = collect([]);
            $relatedFlashFiles = collect([]);
            $relatedDrivers = collect([]);
        }

        return view('public.device-detail', compact(
            'device',
            'deviceModel',
            'groupedSpecs',
            'relatedDevices',
            'relatedFirmwares',
            'relatedFlashFiles',
            'relatedDrivers'
        ));
    }

    /**
     * Firmware Directory.
     */
    public function firmware(Request $request): View
    {
        $search = $request->query('search');
        $query = Firmware::published()->with(['brand', 'device']);
        if ($search) {
            $keyword = '%' . $search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('model_number', 'like', $keyword)
                    ->orWhere('csc', 'like', $keyword)
                    ->orWhere('build_number', 'like', $keyword);
            });
        }
        $firmwares = $query->latest('release_date')->paginate(12)->withQueryString();

        return view('public.firmware', compact('search', 'firmwares'));
    }

    /**
     * Firmware Detail Page.
     */
    public function firmwareDetail(string $firmwareSlug): View
    {
        $firmwareModel = Firmware::published()
            ->where('slug', $firmwareSlug)
            ->with(['brand', 'device', 'mirrors'])
            ->first();

        return view('public.firmware-detail', compact('firmwareSlug', 'firmwareModel'));
    }

    /**
     * Flash Files Directory.
     */
    public function flashFiles(Request $request): View
    {
        $search = $request->query('search');
        $query = FlashFile::published()->with(['brand', 'device']);
        if ($search) {
            $keyword = '%' . $search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                    ->orWhere('model_number', 'like', $keyword)
                    ->orWhere('chipset', 'like', $keyword)
                    ->orWhere('flashing_tool', 'like', $keyword);
            });
        }
        $flashFiles = $query->latest('id')->paginate(12)->withQueryString();

        return view('public.flash-files', compact('search', 'flashFiles'));
    }

    /**
     * Flash File Detail Page.
     */
    public function flashFileDetail(string $fileSlug): View
    {
        $fileModel = FlashFile::published()
            ->where('slug', $fileSlug)
            ->with(['brand', 'device', 'mirrors'])
            ->first();

        return view('public.flash-file-detail', compact('fileSlug', 'fileModel'));
    }

    /**
     * Software Tools Directory.
     */
    public function software(Request $request): View
    {
        $search = $request->query('search');
        $query = SoftwareTool::published()->with('mirrors');
        if ($search) {
            $keyword = '%' . $search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('developer', 'like', $keyword)
                    ->orWhere('category', 'like', $keyword);
            });
        }
        $softwareTools = $query->latest('id')->paginate(12)->withQueryString();

        return view('public.software', compact('search', 'softwareTools'));
    }

    /**
     * Software Detail Page.
     */
    public function softwareDetail(string $softwareSlug): View
    {
        $softwareModel = SoftwareTool::published()
            ->where('slug', $softwareSlug)
            ->with('mirrors')
            ->first();

        return view('public.software-detail', compact('softwareSlug', 'softwareModel'));
    }

    /**
     * USB Drivers Directory.
     */
    public function drivers(Request $request): View
    {
        $search = $request->query('search');
        $query = Driver::published()->with(['brand', 'mirrors']);
        if ($search) {
            $keyword = '%' . $search . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', $keyword)
                    ->orWhere('supported_devices', 'like', $keyword)
                    ->orWhere('windows_versions', 'like', $keyword);
            });
        }
        $drivers = $query->latest('id')->paginate(12)->withQueryString();

        return view('public.drivers', compact('search', 'drivers'));
    }

    /**
     * USB Driver Detail Page.
     */
    public function driverDetail(string $driverSlug): View
    {
        $driverModel = Driver::published()
            ->where('slug', $driverSlug)
            ->with(['brand', 'mirrors'])
            ->first();

        return view('public.driver-detail', compact('driverSlug', 'driverModel'));
    }

    /**
     * News Listing.
     */
    public function news(): View
    {
        $posts = \App\Models\Post::published()
            ->with(['author', 'category', 'tags'])
            ->latest('published_at')
            ->paginate(12);

        return view('public.news', compact('posts'));
    }

    /**
     * News Article Detail.
     */
    public function newsDetail(string $articleSlug): View
    {
        $post = \App\Models\Post::where('slug', $articleSlug)->first();

        if ($post) {
            if (!$post->isPublished()) {
                abort(404, 'Article not found or not published.');
            }
            return view('public.article-detail', [
                'articleSlug' => $articleSlug,
                'post' => $post,
            ]);
        }

        if (in_array($articleSlug, ['samsung-oneui-7-global-rollout-schedule', 'google-november-2026-android-security-bulletin'], true)) {
            return view('public.article-detail', compact('articleSlug'));
        }

        abort(404, 'Article not found.');
    }

    /**
     * Tutorials Listing.
     */
    public function tutorials(): View
    {
        $tutorials = \App\Models\Post::published()
            ->where('type', 'tutorial')
            ->with(['author', 'category'])
            ->latest('published_at')
            ->paginate(12);

        return view('public.tutorials', compact('tutorials'));
    }

    /**
     * Tutorial Detail.
     */
    public function tutorialDetail(string $tutorialSlug): View
    {
        $post = \App\Models\Post::where('slug', $tutorialSlug)->first();

        if ($post) {
            if (!$post->isPublished()) {
                abort(404, 'Tutorial not found or not published.');
            }
            return view('public.tutorial-detail', [
                'tutorialSlug' => $tutorialSlug,
                'post' => $post,
            ]);
        }

        if (in_array($tutorialSlug, ['how-to-flash-samsung-firmware-using-odin', 'fix-qualcomm-hs-usb-qdloader-9008-edl-brick'], true)) {
            return view('public.tutorial-detail', compact('tutorialSlug'));
        }

        abort(404, 'Tutorial not found.');
    }

    /**
     * Global Search Page.
     */
    public function search(Request $request, \App\Services\SearchService $searchService): View
    {
        $query = trim($request->query('q', ''));
        $type = $request->query('type', 'all');
        $page = (int) $request->query('page', 1);

        $searchData = $searchService->search($query, $type, 12, $page);

        return view('public.search', [
            'query' => $searchData['query'],
            'type' => $searchData['type'],
            'results' => $searchData['paginator'],
            'tabCounts' => $searchData['tab_counts'],
            'totalResults' => $searchData['total_results'],
        ]);
    }

    /**
     * Live Search Suggestions JSON Endpoint.
     */
    public function searchSuggestions(Request $request, \App\Services\SearchService $searchService): JsonResponse
    {
        $query = trim($request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([
                'devices' => [],
                'firmware' => [],
                'flash_files' => [],
                'software' => [],
                'drivers' => [],
                'posts' => [],
            ]);
        }

        $suggestions = $searchService->suggest($query, 6);

        return response()->json($suggestions);
    }

    /**
     * Static Pages.
     */
    public function aboutUs(): View
    {
        $page = \App\Models\Page::published()->where('system_key', 'about')->first();
        return view('public.pages.about', compact('page'));
    }

    public function contactUs(): View
    {
        return view('public.pages.contact');
    }

    public function privacyPolicy(): View
    {
        $page = \App\Models\Page::published()->where('system_key', 'privacy')->first();
        return view('public.pages.privacy', compact('page'));
    }

    public function termsAndConditions(): View
    {
        $page = \App\Models\Page::published()->where('system_key', 'terms')->first();
        return view('public.pages.terms', compact('page'));
    }

    public function dmca(): View
    {
        $page = \App\Models\Page::published()->where('system_key', 'dmca')->first();
        return view('public.pages.dmca', compact('page'));
    }
}
