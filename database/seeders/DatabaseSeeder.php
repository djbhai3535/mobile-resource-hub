<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
            ContentSeeder::class,
            SpecificationSeeder::class,
            DeviceCatalogSeeder::class,
            FirmwareCatalogSeeder::class,
        ]);
    }
}
