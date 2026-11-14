<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceSpecificationValue;
use App\Models\Role;
use App\Models\SpecificationField;
use App\Models\SpecificationGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicSpecificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());
    }

    public function test_admin_can_create_specification_group_and_fields(): void
    {
        // 1. Create Group
        $groupResponse = $this->actingAs($this->adminUser)->post(route('admin.specifications.groups.store'), [
            'name' => 'Biometric Security',
            'slug' => 'biometrics-security',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $groupResponse->assertRedirect(route('admin.specifications.index'));
        $this->assertDatabaseHas('specification_groups', [
            'name' => 'Biometric Security',
            'slug' => 'biometrics-security',
        ]);

        $group = SpecificationGroup::where('slug', 'biometrics-security')->first();

        // 2. Add Field
        $fieldResponse = $this->actingAs($this->adminUser)->post(route('admin.specifications.fields.store'), [
            'group_id' => $group->id,
            'label' => 'Fingerprint Scanner Type',
            'key' => 'security_fingerprint_type',
            'field_type' => 'text',
            'help_text' => 'Ultrasonic or Optical in-display sensor',
            'show_on_public' => true,
            'is_active' => true,
        ]);

        $fieldResponse->assertRedirect(route('admin.specifications.index'));
        $this->assertDatabaseHas('specification_fields', [
            'group_id' => $group->id,
            'label' => 'Fingerprint Scanner Type',
            'key' => 'security_fingerprint_type',
        ]);
    }

    public function test_dynamic_specs_render_in_grouped_specifications_method(): void
    {
        $group = SpecificationGroup::create([
            'name' => 'Audio System',
            'slug' => 'audio-system',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $field = SpecificationField::create([
            'group_id' => $group->id,
            'label' => 'DAC Resolution',
            'key' => 'audio_dac_res',
            'field_type' => 'text',
            'unit' => 'bit/kHz',
            'sort_order' => 1,
            'show_on_public' => true,
            'is_active' => true,
        ]);

        $brand = Brand::create(['name' => 'LG', 'slug' => 'lg', 'status' => 'published']);

        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'V60 ThinQ',
            'slug' => 'lg-v60-thinq',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'discontinued',
        ]);

        DeviceSpecificationValue::create([
            'device_id' => $device->id,
            'field_id' => $field->id,
            'value' => '32-bit/192kHz Quad DAC',
        ]);

        $grouped = $device->getGroupedSpecifications();

        $this->assertCount(1, $grouped);
        $this->assertEquals('Audio System', $grouped[0]['group']->name);
        $this->assertCount(1, $grouped[0]['fields']);
        $this->assertEquals('DAC Resolution', $grouped[0]['fields'][0]['field']->label);
        $this->assertEquals('32-bit/192kHz Quad DAC', $grouped[0]['fields'][0]['value']);
    }

    public function test_deleting_field_blocked_if_values_exist_on_devices(): void
    {
        $group = SpecificationGroup::create(['name' => 'Thermal', 'slug' => 'thermal']);
        $field = SpecificationField::create([
            'group_id' => $group->id,
            'label' => 'Vapor Chamber Area',
            'key' => 'vapor_chamber_area',
        ]);

        $brand = Brand::create(['name' => 'Xiaomi', 'slug' => 'xiaomi', 'status' => 'published']);
        $device = Device::create([
            'brand_id' => $brand->id,
            'name' => 'Xiaomi 14',
            'slug' => 'xiaomi-14',
            'device_category' => 'smartphone',
            'status' => 'published',
            'market_status' => 'available',
        ]);

        DeviceSpecificationValue::create([
            'device_id' => $device->id,
            'field_id' => $field->id,
            'value' => '4000 mm²',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.specifications.fields.destroy', $field));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('specification_fields', ['id' => $field->id]);
    }
}
