<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModelNumber;
use App\Models\DeviceVariant;
use App\Models\DeviceSpecificationValue;
use App\Models\SpecificationField;
use Illuminate\Database\Seeder;

class DeviceCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Brands
        $brands = [
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'short_description' => 'Global electronics leader renowned for Galaxy flagship smartphones, foldable devices, and displays.',
                'full_description' => 'Samsung Electronics is a South Korean multinational major technology company headquartered in Suwon, South Korea. Known globally for its flagship Galaxy S series, Galaxy Z foldables, and industry-leading mobile displays and cameras.',
                'website_url' => 'https://www.samsung.com',
                'country_of_origin' => 'South Korea',
                'status' => 'published',
                'sort_order' => 1,
            ],
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'short_description' => 'Creator of iPhone, iOS, Apple Silicon, and premier consumer technology ecosystem.',
                'full_description' => 'Apple Inc. is an American multinational corporation and technology company headquartered in Cupertino, California, in Silicon Valley. It designs, develops, and sells consumer electronics including the iPhone, iPad, and Mac computers.',
                'website_url' => 'https://www.apple.com',
                'country_of_origin' => 'United States',
                'status' => 'published',
                'sort_order' => 2,
            ],
            [
                'name' => 'Xiaomi',
                'slug' => 'xiaomi',
                'short_description' => 'Pioneer in consumer electronics, hyper-charge innovations, and Leica co-engineered mobile cameras.',
                'full_description' => 'Xiaomi Corporation is a Chinese designer and manufacturer of consumer electronics and related software, home appliances, and household hardware. Famous for high-performance smartphones with competitive pricing.',
                'website_url' => 'https://www.mi.com',
                'country_of_origin' => 'China',
                'status' => 'published',
                'sort_order' => 3,
            ],
            [
                'name' => 'Google',
                'slug' => 'google',
                'short_description' => 'Developer of Android OS, Google Tensor silicon, and benchmark Pixel computational photography.',
                'full_description' => 'Google Pixel is a brand of portable consumer electronic devices developed by Google that run either ChromeOS or the Android operating system. Known for pure Android experience, on-device Gemini AI, and computational photography.',
                'website_url' => 'https://store.google.com',
                'country_of_origin' => 'United States',
                'status' => 'published',
                'sort_order' => 4,
            ],
            [
                'name' => 'OnePlus',
                'slug' => 'oneplus',
                'short_description' => 'Performance-driven smartphone innovator offering fluid displays and Hasselblad mobile imaging.',
                'full_description' => 'OnePlus Technology is a Chinese consumer electronics manufacturer headquartered in Shenzhen, Guangdong. Known for its tagline Never Settle, fast charging, and smooth OxygenOS performance.',
                'website_url' => 'https://www.oneplus.com',
                'country_of_origin' => 'China',
                'status' => 'published',
                'sort_order' => 5,
            ],
        ];

        $brandModels = [];
        foreach ($brands as $bData) {
            $brandModels[$bData['slug']] = Brand::firstOrCreate(
                ['slug' => $bData['slug']],
                $bData
            );
        }

        // Cache specification fields for quick lookup by key
        $fields = SpecificationField::all()->keyBy('key');

        // 2. Create Devices
        // Device 1: Samsung Galaxy S24 Ultra
        $s24 = Device::firstOrCreate(
            ['slug' => 'samsung-galaxy-s24-ultra'],
            [
                'brand_id' => $brandModels['samsung']->id,
                'name' => 'Galaxy S24 Ultra',
                'marketing_name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'short_description' => 'Titanium frame powerhouse with Snapdragon 8 Gen 3 for Galaxy, 200MP Quad Tele camera, and Galaxy AI.',
                'product_description' => 'The Samsung Galaxy S24 Ultra represents the pinnacle of modern mobile engineering. Featuring a durable titanium frame, a flat 6.8-inch Dynamic AMOLED 2X display with Corning Gorilla Armor anti-reflective glass, integrated S Pen, and deep on-device Galaxy AI capabilities.',
                'announced_date' => '2024-01-17',
                'release_date' => '2024-01-24',
                'status' => 'published',
                'market_status' => 'available',
                'os' => 'Android',
                'os_version' => 'Android 14, One UI 6.1 (upgradable to 7 major OS updates)',
                'device_category' => 'smartphone',
                'official_product_url' => 'https://www.samsung.com/galaxy-s24-ultra/',
                'is_featured' => true,
                'sort_order' => 1,
                'seo_title' => 'Samsung Galaxy S24 Ultra - Full Phone Specifications & Features',
                'meta_description' => 'Samsung Galaxy S24 Ultra specs: 6.8" 120Hz Dynamic AMOLED 2X, Snapdragon 8 Gen 3, 200MP camera, 5000 mAh battery, S Pen, Titanium build.',
            ]
        );

        // Model numbers for S24 Ultra
        $s24Models = [
            ['model' => 'SM-S928B', 'region' => 'Global', 'primary' => true],
            ['model' => 'SM-S928B/DS', 'region' => 'Global Dual-SIM', 'primary' => false],
            ['model' => 'SM-S928U', 'region' => 'USA Carrier', 'primary' => false],
            ['model' => 'SM-S928U1', 'region' => 'USA Unlocked', 'primary' => false],
            ['model' => 'SM-S928W', 'region' => 'Canada', 'primary' => false],
            ['model' => 'SM-S9280', 'region' => 'China/Hong Kong', 'primary' => false],
        ];
        foreach ($s24Models as $m) {
            DeviceModelNumber::firstOrCreate(
                ['device_id' => $s24->id, 'model_number' => $m['model']],
                ['region' => $m['region'], 'is_primary' => $m['primary']]
            );
        }

        // Variants for S24 Ultra
        $s24Variants = [
            ['name' => '256GB / 12GB RAM', 'ram' => '12GB', 'internal_storage' => '256GB', 'color' => 'Titanium Gray', 'notes' => '$1,299 MSRP'],
            ['name' => '512GB / 12GB RAM', 'ram' => '12GB', 'internal_storage' => '512GB', 'color' => 'Titanium Black', 'notes' => '$1,419 MSRP'],
            ['name' => '1TB / 12GB RAM', 'ram' => '12GB', 'internal_storage' => '1TB', 'color' => 'Titanium Violet', 'notes' => '$1,659 MSRP'],
        ];
        foreach ($s24Variants as $v) {
            DeviceVariant::firstOrCreate(
                ['device_id' => $s24->id, 'name' => $v['name']],
                $v
            );
        }

        // Spec values for S24 Ultra
        $s24Specs = [
            'network_technology' => 'GSM / CDMA / HSPA / EVDO / LTE / 5G',
            'network_speed' => 'HSPA, LTE-A (up to 7CA), 5G',
            'launch_announced' => '2024, January 17',
            'launch_market_status' => 'Available. Released 2024, January 24',
            'body_dimensions' => '162.3 x 79 x 8.6 mm (6.39 x 3.11 x 0.34 in)',
            'body_weight' => '232 g or 233 g (8.18 oz)',
            'body_build' => 'Glass front (Corning Gorilla Armor), glass back (Gorilla Glass), titanium frame (grade 2)',
            'body_sim' => 'Nano-SIM and eSIM or Dual SIM (2 Nano-SIMs and eSIM, dual stand-by)',
            'body_resistance' => 'IP68 dust/water resistant (up to 1.5m for 30 min), Stylus with Bluetooth integration, 2.8ms latency',
            'display_type' => 'Dynamic LTPO AMOLED 2X, 120Hz, HDR10+, 2600 nits (peak)',
            'display_size' => '6.8 inches, 113.5 cm2 (~88.5% screen-to-body ratio)',
            'display_resolution' => '1440 x 3120 pixels, 19.5:9 ratio (~505 ppi density)',
            'display_protection' => 'Corning Gorilla Armor (Anti-reflective 75% reduction)',
            'display_refresh_rate' => '1Hz - 120Hz adaptive',
            'display_peak_brightness' => '2600 nits',
            'platform_os' => 'Android 14, up to 7 major Android upgrades, One UI 6.1',
            'platform_chipset' => 'Qualcomm SM8650-AC Snapdragon 8 Gen 3 for Galaxy (4 nm)',
            'platform_cpu' => '8-core (1x3.39GHz Cortex-X4 & 3x3.1GHz Cortex-A720 & 2x2.9GHz Cortex-A720 & 2x2.2GHz Cortex-A520)',
            'platform_gpu' => 'Adreno 750 (1 GHz)',
            'memory_card_slot' => 'No',
            'memory_internal' => '256GB 12GB RAM, 512GB 12GB RAM, 1TB 12GB RAM (UFS 4.0)',
            'main_camera_setup' => 'Quad Camera System',
            'main_camera_modules' => "200 MP, f/1.7, 24mm (wide), 1/1.3\", 0.6µm, multi-directional PDAF, Laser AF, OIS\n50 MP, f/3.4, 111mm (periscope telephoto), 1/2.52\", 0.7µm, PDAF, OIS, 5x optical zoom\n10 MP, f/2.4, 67mm (telephoto), 1/3.52\", 1.12µm, Dual Pixel PDAF, OIS, 3x optical zoom\n12 MP, f/2.2, 13mm, 120˚ (ultrawide), 1/2.55\", 1.4µm, Dual Pixel PDAF, Super Steady video",
            'main_camera_features' => 'Laser AF, LED flash, auto-HDR, panorama',
            'main_camera_video' => '8K@24/30fps, 4K@30/60/120fps, 1080p@30/60/240fps, 1080p@960fps, HDR10+, stereo sound rec., gyro-EIS',
            'selfie_camera_setup' => 'Single',
            'selfie_camera_modules' => '12 MP, f/2.2, 26mm (wide), Dual Pixel PDAF',
            'selfie_camera_features' => 'Dual video call, Auto-HDR, HDR10+',
            'selfie_camera_video' => '4K@30/60fps, 1080p@30fps',
            'sound_loudspeaker' => 'Yes, with stereo speakers (tuned by AKG)',
            'sound_headphone_jack' => 'No (32-bit/384kHz audio)',
            'comms_wlan' => 'Wi-Fi 802.11 a/b/g/n/ac/6e/7, tri-band, Wi-Fi Direct',
            'comms_bluetooth' => '5.3, A2DP, LE',
            'comms_positioning' => 'GPS, GLONASS, BDS, GALILEO, QZSS',
            'comms_nfc' => 'Yes',
            'comms_usb' => 'USB Type-C 3.2, DisplayPort 1.2, OTG',
            'sensors_list' => 'Fingerprint (under display, ultrasonic), accelerometer, gyro, proximity, compass, barometer, Samsung DeX, Samsung Wireless DeX, Ultra Wideband (UWB) support',
            'battery_type' => 'Li-Ion',
            'battery_capacity' => '5000 mAh, non-removable',
            'charging_wired' => '45W wired, PD3.0, 65% in 30 min (advertised)',
            'charging_wireless' => '15W wireless (Qi/PMA)',
            'charging_reverse' => '4.5W reverse wireless',
            'misc_colors' => 'Titanium Black, Titanium Gray, Titanium Violet, Titanium Yellow, Titanium Blue, Titanium Green, Titanium Orange',
            'features_biometrics' => 'Ultrasonic Under-display Fingerprint, 2D Face Recognition',
            'features_special' => 'Galaxy AI: Circle to Search, Live Translate, Note Assist, Photo Assist; 7 years of OS and security updates.',
        ];

        foreach ($s24Specs as $fieldKey => $val) {
            if (isset($fields[$fieldKey])) {
                DeviceSpecificationValue::firstOrCreate(
                    [
                        'device_id' => $s24->id,
                        'field_id' => $fields[$fieldKey]->id,
                    ],
                    ['value' => $val]
                );
            }
        }

        // Device 2: Apple iPhone 16 Pro Max
        $iphone = Device::firstOrCreate(
            ['slug' => 'apple-iphone-16-pro-max'],
            [
                'brand_id' => $brandModels['apple']->id,
                'name' => 'iPhone 16 Pro Max',
                'marketing_name' => 'Apple iPhone 16 Pro Max',
                'slug' => 'apple-iphone-16-pro-max',
                'short_description' => 'Flagship iPhone featuring Grade 5 Titanium, A18 Pro chip, 48MP Fusion camera, and Camera Control.',
                'product_description' => 'iPhone 16 Pro Max is forged in titanium and powered by the trailblazing A18 Pro chip. Features Camera Control for fast access to visual intelligence, 4K 120 fps Dolby Vision, and the best iPhone battery life ever.',
                'announced_date' => '2024-09-09',
                'release_date' => '2024-09-20',
                'status' => 'published',
                'market_status' => 'available',
                'os' => 'iOS',
                'os_version' => 'iOS 18, upgradable',
                'device_category' => 'smartphone',
                'official_product_url' => 'https://www.apple.com/iphone-16-pro/',
                'is_featured' => true,
                'sort_order' => 2,
                'seo_title' => 'Apple iPhone 16 Pro Max - Full Specifications, Features & Pricing',
                'meta_description' => 'Apple iPhone 16 Pro Max specifications: 6.9" Super Retina XDR OLED, Apple A18 Pro, 48MP triple camera, 4685 mAh battery, Titanium build.',
            ]
        );

        DeviceModelNumber::firstOrCreate(
            ['device_id' => $iphone->id, 'model_number' => 'A3296'],
            ['region' => 'Global', 'is_primary' => true]
        );
        DeviceModelNumber::firstOrCreate(
            ['device_id' => $iphone->id, 'model_number' => 'A3084'],
            ['region' => 'USA', 'is_primary' => false]
        );

        DeviceVariant::firstOrCreate(
            ['device_id' => $iphone->id, 'name' => '256GB / 8GB RAM'],
            ['ram' => '8GB', 'internal_storage' => '256GB', 'color' => 'Natural Titanium', 'notes' => '$1,199 MSRP']
        );
        DeviceVariant::firstOrCreate(
            ['device_id' => $iphone->id, 'name' => '512GB / 8GB RAM'],
            ['ram' => '8GB', 'internal_storage' => '512GB', 'color' => 'Desert Titanium', 'notes' => '$1,399 MSRP']
        );

        $iphoneSpecs = [
            'network_technology' => 'GSM / CDMA / HSPA / EVDO / LTE / 5G',
            'display_type' => 'LTPO Super Retina XDR OLED, 120Hz, HDR10, Dolby Vision, 2000 nits (peak)',
            'display_size' => '6.9 inches, 115.6 cm2 (~91.4% screen-to-body ratio)',
            'display_resolution' => '1320 x 2868 pixels, 19.5:9 ratio (~460 ppi density)',
            'platform_os' => 'iOS 18',
            'platform_chipset' => 'Apple A18 Pro (3 nm)',
            'battery_capacity' => '4685 mAh',
            'charging_wired' => 'Wired, PD2.0, 50% in 30 min (advertised)',
            'charging_wireless' => '25W wireless (MagSafe)',
        ];

        foreach ($iphoneSpecs as $fieldKey => $val) {
            if (isset($fields[$fieldKey])) {
                DeviceSpecificationValue::firstOrCreate(
                    [
                        'device_id' => $iphone->id,
                        'field_id' => $fields[$fieldKey]->id,
                    ],
                    ['value' => $val]
                );
            }
        }

        // Device 3: Google Pixel 9 Pro XL
        $pixel = Device::firstOrCreate(
            ['slug' => 'google-pixel-9-pro-xl'],
            [
                'brand_id' => $brandModels['google']->id,
                'name' => 'Pixel 9 Pro XL',
                'marketing_name' => 'Google Pixel 9 Pro XL',
                'slug' => 'google-pixel-9-pro-xl',
                'short_description' => 'Google flagship powered by Tensor G4 with 16GB RAM, Super Actua display, and Gemini AI built in.',
                'product_description' => 'The Google Pixel 9 Pro XL brings a refined modern design with silky matte glass back and polished metal frame. Powered by Google Tensor G4, equipped with pro-tier triple rear camera setup and Gemini Live capabilities.',
                'announced_date' => '2024-08-13',
                'release_date' => '2024-08-22',
                'status' => 'published',
                'market_status' => 'available',
                'os' => 'Android',
                'os_version' => 'Android 14 (upgradable to 7 major updates)',
                'device_category' => 'smartphone',
                'official_product_url' => 'https://store.google.com/product/pixel_9_pro',
                'is_featured' => true,
                'sort_order' => 3,
                'seo_title' => 'Google Pixel 9 Pro XL - Specs, Features, Camera & Price',
                'meta_description' => 'Google Pixel 9 Pro XL full specifications: 6.8" Super Actua OLED 120Hz, Google Tensor G4, 50MP triple camera, 5060 mAh battery.',
            ]
        );

        DeviceModelNumber::firstOrCreate(
            ['device_id' => $pixel->id, 'model_number' => 'GEC77'],
            ['region' => 'Global', 'is_primary' => true]
        );

        DeviceVariant::firstOrCreate(
            ['device_id' => $pixel->id, 'name' => '128GB / 16GB RAM'],
            ['ram' => '16GB', 'internal_storage' => '128GB', 'color' => 'Obsidian', 'notes' => '$1,099 MSRP']
        );
    }
}
