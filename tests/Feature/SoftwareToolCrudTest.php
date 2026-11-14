<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SoftwareTool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftwareToolCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(Role::where('name', 'super-admin')->first());

        $this->unauthorizedUser = User::factory()->create();
        $this->unauthorizedUser->roles()->attach(Role::where('name', 'author')->first());
    }

    public function test_admin_can_view_software_index(): void
    {
        SoftwareTool::create([
            'name' => 'Samsung Odin',
            'category' => 'flashing_tools',
            'version' => '3.14.4',
            'developer' => 'Samsung',
            'license_type' => 'Freeware',
            'file_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.software.index'));

        $response->assertStatus(200);
        $response->assertSee('Samsung Odin');
        $response->assertSee('3.14.4');
    }

    public function test_admin_can_create_software_with_mirrors(): void
    {
        $payload = [
            'name' => 'SP Flash Tool',
            'slug' => 'sp-flash-tool-test',
            'category' => 'flashing_tools',
            'version' => 'v6.2216',
            'developer' => 'MediaTek',
            'file_format' => '.zip',
            'license_type' => 'Freeware',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
            'mirrors' => [
                [
                    'mirror_name' => 'Primary Mirror',
                    'url' => 'https://drive.google.com/file/d/test-tool/view',
                    'is_official' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.software.store'), $payload);

        $response->assertRedirect(route('admin.software.index'));
        $this->assertDatabaseHas('software_tools', [
            'name' => 'SP Flash Tool',
            'category' => 'flashing_tools',
            'version' => 'v6.2216',
            'status' => 'published',
        ]);

        $tool = SoftwareTool::where('slug', 'sp-flash-tool-test')->first();
        $this->assertNotNull($tool);
        $this->assertCount(1, $tool->mirrors);
    }

    public function test_admin_can_update_software(): void
    {
        $tool = SoftwareTool::create([
            'name' => 'Old Tool Name',
            'category' => 'android_utilities',
            'version' => 'v1.0',
            'license_type' => 'Freeware',
            'file_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->adminUser)->put(route('admin.software.update', $tool), [
            'name' => 'Updated Tool Name',
            'category' => 'android_utilities',
            'version' => 'v2.0',
            'license_type' => 'Open Source',
            'file_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.software.index'));
        $this->assertDatabaseHas('software_tools', [
            'id' => $tool->id,
            'name' => 'Updated Tool Name',
            'version' => 'v2.0',
            'status' => 'published',
        ]);
    }

    public function test_admin_can_soft_delete_and_restore_software(): void
    {
        $tool = SoftwareTool::create([
            'name' => 'Tool To Trash',
            'category' => 'flashing_tools',
            'license_type' => 'Freeware',
            'file_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $delResponse = $this->actingAs($this->adminUser)->delete(route('admin.software.destroy', $tool));
        $delResponse->assertRedirect(route('admin.software.index'));
        $this->assertSoftDeleted('software_tools', ['id' => $tool->id]);

        $restoreResponse = $this->actingAs($this->adminUser)->post(route('admin.software.restore', $tool->id));
        $restoreResponse->assertRedirect(route('admin.software.index', ['trashed' => 1]));
        $this->assertNotSoftDeleted('software_tools', ['id' => $tool->id]);
    }

    public function test_public_user_can_view_published_software(): void
    {
        $tool = SoftwareTool::create([
            'name' => 'Public Live Tool',
            'slug' => 'public-live-tool',
            'category' => 'android_utilities',
            'license_type' => 'Freeware',
            'file_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
        ]);

        $response = $this->get(route('software.show', $tool->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Live Tool');
    }

    public function test_unauthorized_user_cannot_manage_software(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('admin.software.index'));
        $response->assertStatus(403);
    }
}
