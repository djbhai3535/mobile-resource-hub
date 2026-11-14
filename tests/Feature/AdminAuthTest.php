<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_login_success_with_session_regeneration(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@mobilehub.local',
            'password' => 'Admin@MobileHub2026!',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();

        // Verify activity log recorded
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'login',
        ]);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@mobilehub.local',
            'password' => 'WrongPassword123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_regular_user_without_admin_role_cannot_login(): void
    {
        $regularUser = User::create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'regular@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_logout_invalidates_session(): void
    {
        $admin = User::where('email', 'admin@mobilehub.local')->first();

        $response = $this->actingAs($admin)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'logout',
        ]);
    }

    public function test_password_reset_request_records_action(): void
    {
        $response = $this->post('/admin/password/email', [
            'email' => 'admin@mobilehub.local',
        ]);

        $response->assertSessionHas('status');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'password_reset_request',
        ]);
    }
}
