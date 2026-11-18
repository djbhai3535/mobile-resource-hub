<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationIdempotencyTest extends TestCase
{
    protected string $testDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDbPath = database_path('test_migration_recovery.sqlite');
        if (File::exists($this->testDbPath)) {
            File::delete($this->testDbPath);
        }
        touch($this->testDbPath);

        config(['database.connections.migration_test' => [
            'driver' => 'sqlite',
            'database' => $this->testDbPath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testDbPath)) {
            File::delete($this->testDbPath);
        }
        parent::tearDown();
    }

    public function test_migrations_run_successfully_on_fresh_empty_database(): void
    {
        Artisan::call('migrate', [
            '--database' => 'migration_test',
            '--path' => 'database/migrations',
            '--force' => true,
        ]);

        $this->assertTrue(Schema::connection('migration_test')->hasTable('firmwares'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('download_mirrors'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('download_requests'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('flash_files'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('software_tools'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('drivers'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('search_documents'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('themes'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('homepage_sections'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('advertisements'));
    }

    public function test_migrations_succeed_when_firmware_and_mirror_tables_already_exist(): void
    {
        // 1. First run all migrations up to specifications
        // To precisely recreate the VPS state, we run migrations through 2026_11_14_000012
        $allMigrationFiles = glob(database_path('migrations/*.php'));
        sort($allMigrationFiles);

        foreach ($allMigrationFiles as $filePath) {
            $baseName = basename($filePath);
            if (str_contains($baseName, '000013_create_firmware_tables')) {
                break; // Stop right before firmware migration
            }
            Artisan::call('migrate', [
                '--database' => 'migration_test',
                '--path' => 'database/migrations/' . $baseName,
                '--force' => true,
            ]);
        }

        // Verify pre-conditions: brands and devices exist, but firmwares does not yet have migration recorded
        $this->assertTrue(Schema::connection('migration_test')->hasTable('brands'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('devices'));

        // 2. Simulate the state on the VPS:
        // 'firmwares', 'download_mirrors', 'download_requests' were created in MySQL, but the migration itself failed/is pending
        if (!Schema::connection('migration_test')->hasTable('firmwares')) {
            Schema::connection('migration_test')->create('firmwares', function ($table) {
                $table->id();
                $table->foreignId('brand_id')->constrained('brands');
                $table->foreignId('device_id')->nullable()->constrained('devices');
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('model_number');
                $table->timestamps();
            });
        }

        if (!Schema::connection('migration_test')->hasTable('download_mirrors')) {
            Schema::connection('migration_test')->create('download_mirrors', function ($table) {
                $table->id();
                $table->morphs('mirrorable');
                $table->string('mirror_name');
                $table->timestamps();
            });
        }

        if (!Schema::connection('migration_test')->hasTable('download_requests')) {
            Schema::connection('migration_test')->create('download_requests', function ($table) {
                $table->id();
                $table->morphs('downloadable');
                $table->foreignId('mirror_id')->nullable()->constrained('download_mirrors');
                $table->string('status')->default('initiated');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // Insert dummy data into existing tables to verify data is not lost or deleted
        DB::connection('migration_test')->table('brands')->insert([
            'id' => 999,
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('migration_test')->table('firmwares')->insert([
            'id' => 101,
            'brand_id' => 999,
            'title' => 'Existing Firmware Record',
            'slug' => 'existing-firmware-record',
            'model_number' => 'SM-S928B',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Now run the full migration command on this database
        // In the original buggy state, this would crash with SQLSTATE[42S01] Table 'firmwares' already exists
        $exitCode = Artisan::call('migrate', [
            '--database' => 'migration_test',
            '--path' => 'database/migrations',
            '--force' => true,
        ]);

        $this->assertSame(0, $exitCode, 'Migration should complete with exit code 0 when tables already exist.');

        // 4. Verify existing data was untouched and preserved
        $existingFirmware = DB::connection('migration_test')->table('firmwares')->find(101);
        $this->assertNotNull($existingFirmware);
        $this->assertSame('Existing Firmware Record', $existingFirmware->title);

        // 5. Verify all later migrations also ran successfully
        $this->assertTrue(Schema::connection('migration_test')->hasTable('flash_files'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('software_tools'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('drivers'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('search_documents'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('themes'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('homepage_sections'));
        $this->assertTrue(Schema::connection('migration_test')->hasTable('advertisements'));

        // 6. Verify migration tracking table registered the migration
        $hasRunMigration = DB::connection('migration_test')
            ->table('migrations')
            ->where('migration', '2026_11_14_000013_create_firmware_tables')
            ->exists();
        $this->assertTrue($hasRunMigration, 'The migration 2026_11_14_000013_create_firmware_tables must be marked as ran.');

        // 7. Verify forward migration 2026_11_18_000001_ensure_download_requests_composite_index
        $hasForwardMigration = DB::connection('migration_test')
            ->table('migrations')
            ->where('migration', '2026_11_18_000001_ensure_download_requests_composite_index')
            ->exists();
        $this->assertTrue($hasForwardMigration, 'The forward migration must be recorded as ran.');

        // 8. Verify the index exists on download_requests
        $indexes = Schema::connection('migration_test')->getIndexes('download_requests');
        $indexNames = array_column($indexes, 'name');
        $this->assertContains('dl_req_dl_type_dl_id_created_idx', $indexNames);
    }
}
