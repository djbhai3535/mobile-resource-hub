<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Services\AdvertisementService;
use App\Services\HomepageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_standalone_500_view_renders_cleanly(): void
    {
        $view = view('errors.500')->render();
        $this->assertStringContainsString('500 — Server Error', $view);
        $this->assertStringContainsString('Mobile Resource Hub', $view);
    }

    public function test_advertisement_render_returns_empty_string_on_invalid_data_without_exception(): void
    {
        $adService = app(AdvertisementService::class);
        $html = $adService->render('non_existent_placement_xyz');
        $this->assertSame('', $html);
    }

    public function test_homepage_hydrate_section_data_returns_empty_collection_on_unknown_section(): void
    {
        $service = app(HomepageService::class);
        $section = new HomepageSection(['section_key' => 'unknown_invalid_key_xyz', 'item_count' => 5]);
        $data = $service->hydrateSectionData($section);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $data);
        $this->assertTrue($data->isEmpty());
    }

    public function test_diagnostics_endpoint_requires_secret_key(): void
    {
        // Diagnostics file exists in public/
        $diagPath = public_path('diagnostics.php');
        $this->assertFileExists($diagPath);

        // Run with incorrect key
        ob_start();
        $_GET['key'] = 'invalid_key';
        include $diagPath;
        $output = ob_get_clean();

        $this->assertStringContainsString('Forbidden', $output);
    }

    public function test_diagnostics_endpoint_returns_json_with_valid_key(): void
    {
        $diagPath = public_path('diagnostics.php');

        ob_start();
        $_GET['key'] = 'mrh_audit_2026';
        include $diagPath;
        $output = ob_get_clean();

        $data = json_decode($output, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('php_version', $data);
        $this->assertArrayHasKey('checks', $data);
        $this->assertArrayHasKey('writable_directories', $data['checks']);
    }

    public function test_logging_configuration_has_ignore_exceptions_enabled(): void
    {
        $loggingConfig = config('logging.channels.stack');
        $this->assertTrue($loggingConfig['ignore_exceptions'], 'Stack logging must set ignore_exceptions to true so log write failures never crash the app.');
    }
}
