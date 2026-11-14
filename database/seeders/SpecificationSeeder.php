<?php

namespace Database\Seeders;

use App\Models\SpecificationGroup;
use App\Models\SpecificationField;
use Illuminate\Database\Seeder;

class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specifications = [
            [
                'name' => 'Network',
                'slug' => 'network',
                'sort_order' => 1,
                'fields' => [
                    ['label' => 'Technology', 'key' => 'network_technology', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => '2G Bands', 'key' => 'network_2g_bands', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => '3G Bands', 'key' => 'network_3g_bands', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => '4G Bands', 'key' => 'network_4g_bands', 'field_type' => 'textarea', 'sort_order' => 4],
                    ['label' => '5G Bands', 'key' => 'network_5g_bands', 'field_type' => 'textarea', 'sort_order' => 5],
                    ['label' => 'Speed', 'key' => 'network_speed', 'field_type' => 'text', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Launch',
                'slug' => 'launch',
                'sort_order' => 2,
                'fields' => [
                    ['label' => 'Announced', 'key' => 'launch_announced', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Market Status', 'key' => 'launch_market_status', 'field_type' => 'text', 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Body',
                'slug' => 'body',
                'sort_order' => 3,
                'fields' => [
                    ['label' => 'Dimensions', 'key' => 'body_dimensions', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Weight', 'key' => 'body_weight', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'Build Material', 'key' => 'body_build', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'SIM Configuration', 'key' => 'body_sim', 'field_type' => 'text', 'sort_order' => 4],
                    ['label' => 'Water & Dust Resistance', 'key' => 'body_resistance', 'field_type' => 'text', 'sort_order' => 5],
                ]
            ],
            [
                'name' => 'Display',
                'slug' => 'display',
                'sort_order' => 4,
                'fields' => [
                    ['label' => 'Display Type', 'key' => 'display_type', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Screen Size', 'key' => 'display_size', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'Resolution', 'key' => 'display_resolution', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'Glass Protection', 'key' => 'display_protection', 'field_type' => 'text', 'sort_order' => 4],
                    ['label' => 'Refresh Rate', 'key' => 'display_refresh_rate', 'field_type' => 'text', 'sort_order' => 5],
                    ['label' => 'Peak Brightness', 'key' => 'display_peak_brightness', 'field_type' => 'text', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Platform',
                'slug' => 'platform',
                'sort_order' => 5,
                'fields' => [
                    ['label' => 'Operating System', 'key' => 'platform_os', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Chipset', 'key' => 'platform_chipset', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'CPU Processor', 'key' => 'platform_cpu', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'GPU Graphics', 'key' => 'platform_gpu', 'field_type' => 'text', 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Memory',
                'slug' => 'memory',
                'sort_order' => 6,
                'fields' => [
                    ['label' => 'Card Slot', 'key' => 'memory_card_slot', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Internal Storage & RAM', 'key' => 'memory_internal', 'field_type' => 'text', 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Main Camera',
                'slug' => 'main-camera',
                'sort_order' => 7,
                'fields' => [
                    ['label' => 'Camera Setup', 'key' => 'main_camera_setup', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Modules & Lenses', 'key' => 'main_camera_modules', 'field_type' => 'textarea', 'sort_order' => 2],
                    ['label' => 'Camera Features', 'key' => 'main_camera_features', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'Video Capabilities', 'key' => 'main_camera_video', 'field_type' => 'text', 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Selfie Camera',
                'slug' => 'selfie-camera',
                'sort_order' => 8,
                'fields' => [
                    ['label' => 'Camera Setup', 'key' => 'selfie_camera_setup', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Modules & Lenses', 'key' => 'selfie_camera_modules', 'field_type' => 'textarea', 'sort_order' => 2],
                    ['label' => 'Camera Features', 'key' => 'selfie_camera_features', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'Video Capabilities', 'key' => 'selfie_camera_video', 'field_type' => 'text', 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Sound',
                'slug' => 'sound',
                'sort_order' => 9,
                'fields' => [
                    ['label' => 'Loudspeaker', 'key' => 'sound_loudspeaker', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => '3.5mm Headphone Jack', 'key' => 'sound_headphone_jack', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'Audio Quality', 'key' => 'sound_audio_quality', 'field_type' => 'text', 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Communications',
                'slug' => 'communications',
                'sort_order' => 10,
                'fields' => [
                    ['label' => 'WLAN / Wi-Fi', 'key' => 'comms_wlan', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Bluetooth', 'key' => 'comms_bluetooth', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'Positioning / GPS', 'key' => 'comms_positioning', 'field_type' => 'text', 'sort_order' => 3],
                    ['label' => 'NFC', 'key' => 'comms_nfc', 'field_type' => 'text', 'sort_order' => 4],
                    ['label' => 'Radio', 'key' => 'comms_radio', 'field_type' => 'text', 'sort_order' => 5],
                    ['label' => 'USB Interface', 'key' => 'comms_usb', 'field_type' => 'text', 'sort_order' => 6],
                ]
            ],
            [
                'name' => 'Sensors',
                'slug' => 'sensors',
                'sort_order' => 11,
                'fields' => [
                    ['label' => 'Sensors List', 'key' => 'sensors_list', 'field_type' => 'textarea', 'sort_order' => 1],
                ]
            ],
            [
                'name' => 'Battery',
                'slug' => 'battery',
                'sort_order' => 12,
                'fields' => [
                    ['label' => 'Battery Type', 'key' => 'battery_type', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Capacity', 'key' => 'battery_capacity', 'field_type' => 'text', 'sort_order' => 2],
                ]
            ],
            [
                'name' => 'Charging',
                'slug' => 'charging',
                'sort_order' => 13,
                'fields' => [
                    ['label' => 'Wired Charging', 'key' => 'charging_wired', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Wireless Charging', 'key' => 'charging_wireless', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'Reverse Charging', 'key' => 'charging_reverse', 'field_type' => 'text', 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Miscellaneous',
                'slug' => 'miscellaneous',
                'sort_order' => 14,
                'fields' => [
                    ['label' => 'Color Options', 'key' => 'misc_colors', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Model Designations', 'key' => 'misc_models', 'field_type' => 'text', 'sort_order' => 2],
                    ['label' => 'SAR Specific Absorption', 'key' => 'misc_sar', 'field_type' => 'text', 'sort_order' => 3],
                ]
            ],
            [
                'name' => 'Additional Features',
                'slug' => 'additional-features',
                'sort_order' => 15,
                'fields' => [
                    ['label' => 'Biometric Security', 'key' => 'features_biometrics', 'field_type' => 'text', 'sort_order' => 1],
                    ['label' => 'Special Capabilities & AI', 'key' => 'features_special', 'field_type' => 'textarea', 'sort_order' => 2],
                ]
            ],
        ];

        foreach ($specifications as $groupData) {
            $fields = $groupData['fields'];
            unset($groupData['fields']);

            $group = SpecificationGroup::firstOrCreate(
                ['slug' => $groupData['slug']],
                $groupData
            );

            foreach ($fields as $fieldData) {
                $fieldData['group_id'] = $group->id;
                SpecificationField::firstOrCreate(
                    [
                        'key' => $fieldData['key']
                    ],
                    $fieldData
                );
            }
        }
    }
}
