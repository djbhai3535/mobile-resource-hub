<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
        $this->menu = Menu::create([
            'location' => 'header',
            'name' => 'Main Navigation',
        ]);
    }

    public function test_admin_can_add_menu_item(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/menus/{$this->menu->id}/items", [
            'label' => 'Repair Tools',
            'url' => '/software',
            'target' => '_self',
            'order' => 1,
            'is_active' => 1,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $this->menu->id,
            'label' => 'Repair Tools',
            'url' => '/software',
        ]);
    }

    public function test_malicious_javascript_scheme_in_menu_url_is_blocked(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/menus/{$this->menu->id}/items", [
            'label' => 'XSS Link',
            'url' => 'javascript:alert(document.cookie)',
            'target' => '_self',
            'order' => 1,
        ]);

        $response->assertSessionHasErrors('url');
        $this->assertDatabaseMissing('menu_items', ['label' => 'XSS Link']);
    }
}
