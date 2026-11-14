<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DownloadMirror;
use App\Models\Driver;
use App\Models\Firmware;
use App\Models\FlashFile;
use App\Models\SoftwareTool;
use Illuminate\Database\Seeder;

class FirmwareCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $samsungBrand = Brand::where('slug', 'samsung')->first();
        $xiaomiBrand = Brand::where('slug', 'xiaomi')->first();
        $googleBrand = Brand::where('slug', 'google')->first();

        $s24UltraDevice = Device::where('slug', 'galaxy-s24-ultra')->first();
        $redmiNoteDevice = Device::where('slug', 'redmi-note-13-pro-5g')->first();

        // 1. FIRMWARES
        // Samsung Galaxy S24 Ultra Android 15
        if ($samsungBrand) {
            $s24Fw = Firmware::updateOrCreate(
                ['slug' => 'galaxy-s24-ultra-sm-s928b-oneui7'],
                [
                    'brand_id' => $samsungBrand->id,
                    'device_id' => $s24UltraDevice?->id,
                    'title' => 'Samsung Galaxy S24 Ultra Android 15 OneUI 7.0',
                    'model_number' => 'SM-S928B',
                    'region' => 'Europe',
                    'csc' => 'OXM',
                    'android_version' => 'Android 15',
                    'os_version' => 'One UI 7.0',
                    'firmware_version' => 'S928BXXU3BXK2',
                    'build_number' => 'S928BXXU3BXK2',
                    'security_patch_date' => '2026-11-01',
                    'release_date' => '2026-11-05',
                    'file_type' => 'Stock ROM',
                    'archive_format' => '.tar.md5',
                    'file_size_bytes' => 9928154624,
                    'file_size_formatted' => '9.24 GB',
                    'md5_checksum' => '4a8f93e9812b1c3d90f23a9b9872e41c',
                    'sha256_checksum' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                    'flashing_instructions' => "1. Install Samsung USB Drivers\n2. Open Odin 3.14.4 as Administrator\n3. Put S24 Ultra into Download mode (Vol Down + Up + USB)\n4. Load BL, AP, CP, CSC tar archives\n5. Click Start and wait for PASS notification",
                    'required_tools' => 'Samsung Odin v3.14.4, Samsung USB Driver v1.7.59.0',
                    'prerequisites' => '60% battery charge, full data backup, original USB-C cable',
                    'warnings' => 'Flashing with CSC will factory reset the phone. To keep user data, flash HOME_CSC instead of CSC.',
                    'source_website' => 'Official Samsung Firmware Server (FOTA / Smart Switch)',
                    'official_download_url' => 'https://download.samsung.com/firmware/S928BXXU3BXK2.zip',
                    'license_type' => 'Official OEM Stock ROM',
                    'distribution_permission' => 'authorized_mirror',
                    'is_verified' => true,
                    'status' => 'published',
                    'download_requests_count' => 1428,
                    'seo_title' => 'Galaxy S24 Ultra Android 15 OneUI 7.0 Firmware (SM-S928B) — Mobile Resource Hub',
                    'meta_description' => 'Download official stock firmware build S928BXXU3BXK2 for Samsung Galaxy S24 Ultra SM-S928B. Complete with verified SHA-256 checksum and flashing instructions.',
                ]
            );

            // Mirrors for S24 Ultra
            $s24Fw->mirrors()->delete();
            $s24Fw->mirrors()->createMany([
                [
                    'mirror_name' => 'Official CDN Server (High Speed)',
                    'url' => 'https://download.samsung.com/firmware/S928BXXU3BXK2.zip',
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'mirror_name' => 'Fast Cloud Mirror (EU / Global)',
                    'url' => 'https://drive.google.com/file/d/sample-samsung-firmware-mirror/view',
                    'is_official' => false,
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ]);
        }

        // Xiaomi Redmi Note 13 Pro 5G HyperOS 2.0
        if ($xiaomiBrand) {
            $xiaomiFw = Firmware::updateOrCreate(
                ['slug' => 'redmi-note-13-pro-5g-hyperos2-global'],
                [
                    'brand_id' => $xiaomiBrand->id,
                    'device_id' => $redmiNoteDevice?->id,
                    'title' => 'Redmi Note 13 Pro 5G HyperOS 2.0',
                    'model_number' => '2312DRA50G',
                    'region' => 'Global (MI)',
                    'csc' => 'MI',
                    'android_version' => 'Android 14',
                    'os_version' => 'HyperOS 2.0',
                    'firmware_version' => 'OS2.0.1.0.UNRMIXM',
                    'build_number' => 'OS2.0.1.0.UNRMIXM',
                    'security_patch_date' => '2026-10-01',
                    'release_date' => '2026-10-25',
                    'file_type' => 'Fastboot ROM',
                    'archive_format' => '.tgz',
                    'file_size_bytes' => 6012954210,
                    'file_size_formatted' => '5.60 GB',
                    'md5_checksum' => '7b3c2d1e0f9a8b7c6d5e4f3a2b1c0d9e',
                    'sha256_checksum' => 'a1b2c3d4e5f67890123456789abcdef0123456789abcdef0123456789abcdef0',
                    'flashing_instructions' => "1. Unlock Bootloader with Xiaomi Community tool\n2. Boot phone to Fastboot mode\n3. Run flash_all.bat via Mi Flash Tool\n4. Phone reboots automatically into HyperOS",
                    'required_tools' => 'Mi Flash Tool 2024, Qualcomm HS-USB Drivers',
                    'source_website' => 'Xiaomi Official ROM Server',
                    'official_download_url' => 'https://bigota.d.miui.com/OS2.0.1.0.UNRMIXM/garnet_global_images.tgz',
                    'license_type' => 'Official OEM Stock ROM',
                    'distribution_permission' => 'authorized_mirror',
                    'is_verified' => true,
                    'status' => 'published',
                    'download_requests_count' => 932,
                    'seo_title' => 'Redmi Note 13 Pro 5G HyperOS 2.0 Global Firmware — Mobile Resource Hub',
                    'meta_description' => 'Download official Fastboot ROM for Xiaomi Redmi Note 13 Pro 5G (garnet). Verified checksums and instructions.',
                ]
            );

            $xiaomiFw->mirrors()->delete();
            $xiaomiFw->mirrors()->createMany([
                [
                    'mirror_name' => 'Xiaomi BigOTA Server (Official)',
                    'url' => 'https://bigota.d.miui.com/OS2.0.1.0.UNRMIXM/garnet_global_images.tgz',
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ]);
        }

        // 2. FLASH FILES
        if ($xiaomiBrand) {
            $garnetFlash = FlashFile::updateOrCreate(
                ['slug' => 'redmi-note-13-pro-5g-garnet-fastboot-rom'],
                [
                    'brand_id' => $xiaomiBrand->id,
                    'device_id' => $redmiNoteDevice?->id,
                    'title' => 'Xiaomi Redmi Note 13 Pro 5G Fastboot ROM (garnet)',
                    'model_number' => '2312DRA50G',
                    'chipset' => 'Snapdragon 7s Gen 2',
                    'file_type' => 'stock_rom',
                    'android_version' => 'Android 14',
                    'file_size_bytes' => 6925829120,
                    'file_size_formatted' => '6.45 GB',
                    'md5_checksum' => 'c891ab540f2937ef1234a56b78cd90ef',
                    'sha256_checksum' => '5d41402abc4b2a76b9719d911017c5926c4f7495b5465c0293a9fa937402636a',
                    'flashing_tool' => 'XiaoMiTool V2 / Mi Flash Tool 2024',
                    'compatibility_notes' => 'Tested on European, Indian, and Global 2312DRA50G hardware models.',
                    'installation_instructions' => 'Boot into Fastboot mode (Vol Down + Power). Use clean all script to prevent relocking unless on official region firmware.',
                    'source_website' => 'Xiaomi Official Firmware Archive',
                    'distribution_permission' => 'authorized_mirror',
                    'is_verified' => true,
                    'status' => 'published',
                    'download_requests_count' => 645,
                    'seo_title' => 'Redmi Note 13 Pro 5G Fastboot Flash File (garnet) — Mobile Resource Hub',
                    'meta_description' => 'Download verified Fastboot flash file for Redmi Note 13 Pro 5G with Snapdragon 7s Gen 2 chipset.',
                ]
            );

            $garnetFlash->mirrors()->delete();
            $garnetFlash->mirrors()->createMany([
                [
                    'mirror_name' => 'Fastboot Global CDN',
                    'url' => 'https://bigota.d.miui.com/garnet_global_fastboot.tgz',
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ]);
        }

        // MediaTek Scatter Repair Firmware
        if ($xiaomiBrand) {
            $mtkScatter = FlashFile::updateOrCreate(
                ['slug' => 'mtk-mt6768-scatter-firmware-package'],
                [
                    'brand_id' => $xiaomiBrand->id,
                    'device_id' => null,
                    'title' => 'MediaTek Helio G85 (MT6768) Universal Scatter Flash File',
                    'model_number' => 'MT6768',
                    'chipset' => 'MediaTek Helio G85 (MT6768)',
                    'file_type' => 'scatter_firmware',
                    'android_version' => 'Android 13 / 12',
                    'file_size_bytes' => 4508715008,
                    'file_size_formatted' => '4.20 GB',
                    'md5_checksum' => '2a3b4c5d6e7f8091a2b3c4d5e6f7a8b9',
                    'sha256_checksum' => '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b',
                    'flashing_tool' => 'SP Flash Tool v6.2216',
                    'compatibility_notes' => 'Includes MT6768_Android_scatter.txt and unsigned DA file for unbricking and NVRAM repair.',
                    'installation_instructions' => 'Load MT6768 scatter in SP Flash Tool. Select Download Only mode. Power off device and connect via USB.',
                    'source_website' => 'MediaTek Developer Portal',
                    'distribution_permission' => 'authorized_mirror',
                    'is_verified' => true,
                    'status' => 'published',
                    'download_requests_count' => 1250,
                    'seo_title' => 'MediaTek Helio G85 MT6768 Scatter Flash File — Mobile Resource Hub',
                    'meta_description' => 'Download MediaTek MT6768 Android scatter firmware for unbricking and flashing with SP Flash Tool.',
                ]
            );

            $mtkScatter->mirrors()->delete();
            $mtkScatter->mirrors()->createMany([
                [
                    'mirror_name' => 'MediaTek Verified Mirror',
                    'url' => 'https://developer.mediatek.com/firmware/MT6768_Scatter_Package.zip',
                    'is_official' => true,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ]);
        }

        // 3. SOFTWARE TOOLS
        // Samsung Odin 3.14.4
        $odin = SoftwareTool::updateOrCreate(
            ['slug' => 'odin-3-14'],
            [
                'name' => 'Samsung Odin Flashing Tool',
                'category' => 'flashing_tools',
                'version' => '3.14.4',
                'developer' => 'Samsung Electronics',
                'supported_os' => 'Windows 11, Windows 10, Windows 8.1, Windows 7',
                'supported_architectures' => '32-bit (x86) / 64-bit (x64)',
                'description' => 'Official proprietary PC flashing utility developed by Samsung for internal service centers and device maintenance. Flashes official 4-file / 5-file stock ROM packages (AP, BL, CP, CSC, HOME_CSC) to Samsung Galaxy devices in Download Mode.',
                'changelog' => 'Added support for UFS 4.0 storage partitions, enhanced SHA256 tar signature verification, and full Windows 11 compatibility.',
                'release_date' => '2024-03-15',
                'file_format' => '.zip',
                'file_size_bytes' => 4194304,
                'file_size_formatted' => '4.00 MB',
                'md5_checksum' => '9e107d9d372bb6826bd81d3542a419d6',
                'sha256_checksum' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                'license_type' => 'OEM Proprietary Utility (Freeware)',
                'distribution_permission' => 'authorized_mirror',
                'official_website' => 'https://samsung.com',
                'dependencies' => 'Samsung Android USB Driver, Microsoft Visual C++ 2015 Redistributable',
                'installation_instructions' => "1. Download and extract Odin3_v3.14.4.zip to a folder\n2. Right click Odin3.exe and select Run as administrator\n3. Put your Galaxy device in Download Mode and connect with USB-C cable\n4. When ID:COM turns blue, load your BL, AP, CP, and CSC files\n5. Click Start and wait for PASS!",
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 8420,
                'seo_title' => 'Download Samsung Odin 3.14.4 Flashing Utility — Mobile Resource Hub',
                'meta_description' => 'Download official Samsung Odin 3.14.4 for Windows. Safe, verified checksum, and instructions for flashing Galaxy firmware.',
            ]
        );

        $odin->mirrors()->delete();
        $odin->mirrors()->createMany([
            [
                'mirror_name' => 'High-Speed Cloud Mirror (Primary)',
                'url' => 'https://drive.google.com/file/d/sample-odin-3-14-mirror/view',
                'is_official' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'mirror_name' => 'Fast CDN Mirror (Secondary)',
                'url' => 'https://mega.nz/file/sample-odin-mirror',
                'is_official' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
        ]);

        // SP Flash Tool v6
        $spFlash = SoftwareTool::updateOrCreate(
            ['slug' => 'sp-flash-tool-v6'],
            [
                'name' => 'SP Flash Tool (Smart Phone Flash Tool)',
                'category' => 'flashing_tools',
                'version' => 'v6.2216',
                'developer' => 'MediaTek Inc.',
                'supported_os' => 'Windows 11, 10, 8.1 / Linux x64',
                'supported_architectures' => '64-bit (x64)',
                'description' => 'Standard flashing software for devices powered by MediaTek (MTK) processors. Supports scatter-based ROM flashing, memory testing, parameter formatting, and full chip unbricking.',
                'release_date' => '2024-06-20',
                'file_format' => '.zip',
                'file_size_bytes' => 67108864,
                'file_size_formatted' => '64.0 MB',
                'md5_checksum' => 'f4c3b2a10e9d8c7b6a5f4e3d2c1b0a9f',
                'sha256_checksum' => 'ca978112ca1bbdcafac231b39a23dc4da786eff8147c4e72b9807785afee48bb',
                'license_type' => 'MediaTek Proprietary (Free for Service)',
                'distribution_permission' => 'authorized_mirror',
                'official_website' => 'https://spflashtool.com',
                'dependencies' => 'MediaTek Preloader VCOM Drivers',
                'installation_instructions' => 'Extract archive and run flash_tool.exe. Choose scatter file and authentication file (DA).',
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 4920,
                'seo_title' => 'Download SP Flash Tool v6.2216 for MediaTek — Mobile Resource Hub',
                'meta_description' => 'Download latest SP Flash Tool v6 for Windows & Linux. Unbrick and flash MediaTek devices.',
            ]
        );

        $spFlash->mirrors()->delete();
        $spFlash->mirrors()->createMany([
            [
                'mirror_name' => 'Official SP Flash Tool CDN',
                'url' => 'https://spflashtool.com/download/SP_Flash_Tool_v6.2216_Win.zip',
                'is_official' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        // Google Platform-Tools (ADB & Fastboot)
        $adbTool = SoftwareTool::updateOrCreate(
            ['slug' => 'platform-tools-adb-fastboot'],
            [
                'name' => 'Android SDK Platform-Tools (ADB & Fastboot CLI)',
                'category' => 'android_utilities',
                'version' => 'v35.0.1',
                'developer' => 'Google LLC',
                'supported_os' => 'Windows, macOS, Linux',
                'supported_architectures' => 'Universal x86_64 / arm64',
                'description' => 'Official Android Debug Bridge (adb) and Fastboot command-line tools provided directly by Google for Android developers, technicians, and power users.',
                'release_date' => '2024-04-10',
                'file_format' => '.zip',
                'file_size_bytes' => 14680064,
                'file_size_formatted' => '14.0 MB',
                'md5_checksum' => '5d41402abc4b2a76b9719d911017c592',
                'sha256_checksum' => '2cf24dba5fb0a30e26e83b2ac5b9e29e1b161e5c1fa7425e73043362938b9824',
                'license_type' => 'Apache 2.0 / Android SDK License',
                'distribution_permission' => 'authorized_mirror',
                'official_website' => 'https://developer.android.com/tools/releases/platform-tools',
                'installation_instructions' => 'Extract to C:\\platform-tools and add to your Windows System PATH environment variable.',
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 15400,
                'seo_title' => 'Download Android SDK Platform-Tools (ADB / Fastboot) — Mobile Resource Hub',
                'meta_description' => 'Download official Google Android SDK Platform-Tools with ADB and Fastboot utilities.',
            ]
        );

        $adbTool->mirrors()->delete();
        $adbTool->mirrors()->createMany([
            [
                'mirror_name' => 'Google Official Developer CDN',
                'url' => 'https://dl.google.com/android/repository/platform-tools-latest-windows.zip',
                'is_official' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        // 4. USB DRIVERS
        // Samsung USB Driver
        $samsungDriver = Driver::updateOrCreate(
            ['slug' => 'samsung-usb-driver'],
            [
                'brand_id' => $samsungBrand?->id,
                'name' => 'Samsung USB Driver for Mobile Phones',
                'driver_version' => 'v1.7.59.0',
                'os' => 'Windows',
                'windows_versions' => 'Windows 11, Windows 10, Windows 8.1, Windows 7',
                'architecture' => '32-bit (x86) / 64-bit (x64)',
                'release_date' => '2024-02-18',
                'supported_devices' => 'All Samsung Galaxy Smartphones, Galaxy Tablets, Galaxy Z Fold/Flip series',
                'file_type' => 'EXE Installer',
                'file_size_bytes' => 36909875,
                'file_size_formatted' => '35.2 MB',
                'md5_checksum' => '3b712c9f56e189a01234b5c678d9e0fa',
                'sha256_checksum' => '4b227777d4dd1fc61c6f884f48641d02b4d121d3fd328cb08b5531fcacdabf8a',
                'official_source' => 'https://developer.samsung.com/android-usb-driver',
                'distribution_permission' => 'authorized_mirror',
                'installation_instructions' => "1. Disconnect your Samsung device from computer\n2. Run SAMSUNG_USB_Driver_for_Mobile_Phones.exe as administrator\n3. Select your language and install location\n4. Complete the installation wizard and reboot your PC\n5. Connect your phone via USB cable and allow USB debugging",
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 12900,
                'seo_title' => 'Download Samsung USB Driver for Windows (Official) — Mobile Resource Hub',
                'meta_description' => 'Download official Samsung USB Driver v1.7.59.0 for Windows 11/10/8/7. Digitally signed and tested with Odin.',
            ]
        );

        $samsungDriver->mirrors()->delete();
        $samsungDriver->mirrors()->createMany([
            [
                'mirror_name' => 'Samsung Developer Official Mirror',
                'url' => 'https://developer.samsung.com/file/SAMSUNG_USB_Driver_for_Mobile_Phones.exe',
                'is_official' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'mirror_name' => 'Verified Fast Cloud Mirror',
                'url' => 'https://drive.google.com/file/d/sample-samsung-usb-driver-mirror/view',
                'is_official' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
        ]);

        // Qualcomm HS-USB QDLoader 9008 Driver
        $qualcommDriver = Driver::updateOrCreate(
            ['slug' => 'qualcomm-qdloader-9008-driver'],
            [
                'brand_id' => null, // Universal across Snapdragon devices
                'name' => 'Qualcomm HS-USB QDLoader 9008 Driver',
                'driver_version' => 'v2.1.0.5',
                'os' => 'Windows',
                'windows_versions' => 'Windows 11, Windows 10, Windows 8.1, Windows 7',
                'architecture' => '32-bit (x86) / 64-bit (x64)',
                'release_date' => '2024-01-15',
                'supported_devices' => 'All Qualcomm Snapdragon devices in Emergency Download (EDL 9008) mode',
                'file_type' => 'EXE Installer',
                'file_size_bytes' => 20971520,
                'file_size_formatted' => '20.0 MB',
                'md5_checksum' => '8a7b6c5d4e3f2019a8b7c6d5e4f3a2b1',
                'sha256_checksum' => 'ef2d127de37b942baad06145e54b0c619a1f22327b2ebbcfbec78f5564afe39d',
                'official_source' => 'https://qualcomm.com',
                'distribution_permission' => 'authorized_mirror',
                'installation_instructions' => 'Choose WWAN-DHCP is not used option during setup. Reboot computer to ensure COM port recognition.',
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 9840,
                'seo_title' => 'Download Qualcomm QDLoader 9008 Driver (EDL Mode) — Mobile Resource Hub',
                'meta_description' => 'Download Qualcomm HS-USB QDLoader 9008 drivers for Windows. Essential for unbricking Qualcomm devices with QFIL or Mi Flash.',
            ]
        );

        $qualcommDriver->mirrors()->delete();
        $qualcommDriver->mirrors()->createMany([
            [
                'mirror_name' => 'Primary Signed Mirror',
                'url' => 'https://drive.google.com/file/d/sample-qualcomm-9008-mirror/view',
                'is_official' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);

        // MediaTek MTK Preloader USB VCOM Driver
        $mtkDriver = Driver::updateOrCreate(
            ['slug' => 'mediatek-vcom-usb-driver'],
            [
                'brand_id' => null,
                'name' => 'MediaTek MTK Preloader USB VCOM Driver',
                'driver_version' => 'v1.0.8',
                'os' => 'Windows',
                'windows_versions' => 'Windows 11, Windows 10, Windows 8.1, Windows 7',
                'architecture' => '32-bit (x86) / 64-bit (x64)',
                'release_date' => '2024-03-01',
                'supported_devices' => 'All MediaTek MTK Dimensity and Helio series smartphones in Preloader / BootROM mode',
                'file_type' => 'INF Driver Archive',
                'file_size_bytes' => 12582912,
                'file_size_formatted' => '12.0 MB',
                'md5_checksum' => '1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e',
                'sha256_checksum' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',
                'official_source' => 'https://mediatek.com',
                'distribution_permission' => 'authorized_mirror',
                'installation_instructions' => 'Right click cdc-acm.inf and select Install. Ensure Windows Driver Signature Enforcement is handled if on legacy Windows.',
                'is_verified' => true,
                'status' => 'published',
                'download_requests_count' => 7650,
                'seo_title' => 'Download MediaTek Preloader USB VCOM Driver — Mobile Resource Hub',
                'meta_description' => 'Download MediaTek MTK USB VCOM driver for Windows. Connect Helio and Dimensity devices to SP Flash Tool.',
            ]
        );

        $mtkDriver->mirrors()->delete();
        $mtkDriver->mirrors()->createMany([
            [
                'mirror_name' => 'MediaTek VCOM Mirror',
                'url' => 'https://drive.google.com/file/d/sample-mtk-vcom-mirror/view',
                'is_official' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
        ]);
    }
}
