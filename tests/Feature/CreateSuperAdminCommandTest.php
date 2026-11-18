<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateSuperAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
    }

    public function test_can_create_super_admin_via_options(): void
    {
        $this->artisan('admin:create-super-admin', [
            '--name' => 'Root System Admin',
            '--email' => 'root@editzy.website',
            '--password' => 'SecurePass2026!#',
        ])
        ->assertSuccessful()
        ->expectsOutputToContain('Super Administrator account [root@editzy.website] successfully created');

        $user = User::where('email', 'root@editzy.website')->first();
        $this->assertNotNull($user);
        $this->assertSame('Root System Admin', $user->name);
        $this->assertTrue(Hash::check('SecurePass2026!#', $user->password));
        $this->assertTrue($user->hasRole('super-admin'));
    }

    public function test_rejects_duplicate_account_safely_without_overwriting(): void
    {
        // First create user
        $user = User::factory()->create([
            'email' => 'existing@editzy.website',
            'password' => Hash::make('OriginalPassword123!'),
        ]);

        $superAdminRole = Role::where('name', 'super-admin')->first();
        $user->roles()->attach($superAdminRole);

        // Try creating again with same email
        $this->artisan('admin:create-super-admin', [
            '--name' => 'Hacker Attempter',
            '--email' => 'existing@editzy.website',
            '--password' => 'NewHackedPassword!',
        ])
        ->assertSuccessful()
        ->expectsOutputToContain('This user already holds the [super-admin] role. No changes made.');

        // Verify password remained completely untouched
        $user->refresh();
        $this->assertTrue(Hash::check('OriginalPassword123!', $user->password));
    }

    public function test_validates_short_password(): void
    {
        $this->artisan('admin:create-super-admin', [
            '--name' => 'Admin Short',
            '--email' => 'short@editzy.website',
            '--password' => 'short',
        ])
        ->assertFailed()
        ->expectsOutputToContain('at least 8 characters');
    }
}
