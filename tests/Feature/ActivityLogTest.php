<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogger;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
    }

    public function test_activity_is_logged_on_admin_actions(): void
    {
        ActivityLogger::log('settings_change', null, ['section' => 'branding', 'mode' => 'dark'], $this->admin);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->admin->id,
            'action' => 'settings_change',
        ]);
    }

    public function test_sensitive_credentials_are_redacted_from_audit_logs(): void
    {
        ActivityLogger::log('password_update_attempt', null, [
            'username' => 'admin',
            'password' => 'SuperSecret123!',
            'token' => 'jwt_secret_token_abc',
            'api_key' => 'live_sk_987654321',
        ], $this->admin);

        $log = ActivityLog::where('action', 'password_update_attempt')->first();
        $this->assertNotNull($log);

        $properties = $log->properties;
        $this->assertEquals('[REDACTED]', $properties['password']);
        $this->assertEquals('[REDACTED]', $properties['token']);
        $this->assertEquals('[REDACTED]', $properties['api_key']);
        $this->assertEquals('admin', $properties['username']);
    }
}
