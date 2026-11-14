<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@mobilehub.local'],
            [
                'name' => 'MRH Super Administrator',
                'password' => Hash::make('Admin@MobileHub2026!'),
                'email_verified_at' => now(),
            ]
        );
        $superAdminRole = Role::where('name', 'super-admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // 2. Editor
        $editor = User::firstOrCreate(
            ['email' => 'editor@mobilehub.local'],
            [
                'name' => 'Sarah Jenkins (Editor)',
                'password' => Hash::make('Editor@MobileHub2026!'),
                'email_verified_at' => now(),
            ]
        );
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole) {
            $editor->roles()->syncWithoutDetaching([$editorRole->id]);
        }

        // 3. Author
        $author = User::firstOrCreate(
            ['email' => 'author@mobilehub.local'],
            [
                'name' => 'Alex Rivera (Author)',
                'password' => Hash::make('Author@MobileHub2026!'),
                'email_verified_at' => now(),
            ]
        );
        $authorRole = Role::where('name', 'author')->first();
        if ($authorRole) {
            $author->roles()->syncWithoutDetaching([$authorRole->id]);
        }
    }
}
