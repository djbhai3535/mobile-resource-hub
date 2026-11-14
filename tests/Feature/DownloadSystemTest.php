<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\DownloadMirror;
use App\Models\DownloadRequest;
use App\Models\Firmware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Brand $brand;
    protected Firmware $firmware;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brand = Brand::create([
            'name' => 'Samsung',
            'slug' => 'samsung',
            'status' => 'published',
        ]);

        $this->firmware = Firmware::create([
            'brand_id' => $this->brand->id,
            'title' => 'Galaxy S24 Ultra Android 15 OneUI 7.0',
            'slug' => 'galaxy-s24-ultra-sm-s928b-oneui7',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.tar.md5',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'published',
            'download_requests_count' => 0,
        ]);
    }

    public function test_download_mirror_increments_counter_and_logs_request(): void
    {
        $mirror = $this->firmware->mirrors()->create([
            'mirror_name' => 'Fast Cloud Mirror',
            'url' => 'https://drive.google.com/file/d/test-firmware/view',
            'is_official' => true,
            'is_active' => true,
            'download_requests_count' => 0,
        ]);

        $response = $this->get(route('downloads.mirror', $mirror));

        $response->assertRedirect('https://drive.google.com/file/d/test-firmware/view');

        // Verify request logged
        $this->assertDatabaseHas('download_requests', [
            'mirror_id' => $mirror->id,
            'downloadable_type' => Firmware::class,
            'downloadable_id' => $this->firmware->id,
            'status' => 'redirected',
        ]);

        // Verify counters incremented
        $this->assertEquals(1, $mirror->fresh()->download_requests_count);
        $this->assertEquals(1, $this->firmware->fresh()->download_requests_count);
    }

    public function test_download_mirror_rejects_inactive_mirror(): void
    {
        $mirror = $this->firmware->mirrors()->create([
            'mirror_name' => 'Inactive Mirror',
            'url' => 'https://example.com/file.zip',
            'is_active' => false,
        ]);

        $response = $this->get(route('downloads.mirror', $mirror));
        $response->assertStatus(404);
    }

    public function test_download_mirror_rejects_draft_resource(): void
    {
        $draftFw = Firmware::create([
            'brand_id' => $this->brand->id,
            'title' => 'Draft Firmware',
            'slug' => 'draft-firmware',
            'model_number' => 'SM-S928B',
            'file_type' => 'Stock ROM',
            'archive_format' => '.zip',
            'distribution_permission' => 'authorized_mirror',
            'status' => 'draft',
        ]);

        $mirror = $draftFw->mirrors()->create([
            'mirror_name' => 'Draft Mirror',
            'url' => 'https://example.com/draft.zip',
            'is_active' => true,
        ]);

        $response = $this->get(route('downloads.mirror', $mirror));
        $response->assertStatus(404);
    }

    public function test_ssrf_and_unsafe_redirect_prevention(): void
    {
        // Unsafe localhost target
        $unsafeMirror = $this->firmware->mirrors()->create([
            'mirror_name' => 'Malicious Localhost Target',
            'url' => 'http://127.0.0.1:8000/admin/secrets',
            'is_active' => true,
        ]);

        $response = $this->get(route('downloads.mirror', $unsafeMirror));
        $response->assertStatus(400);

        // Unsafe private IP target
        $privateIpMirror = $this->firmware->mirrors()->create([
            'mirror_name' => 'Private Subnet Target',
            'url' => 'http://192.168.1.1/admin',
            'is_active' => true,
        ]);

        $response2 = $this->get(route('downloads.mirror', $privateIpMirror));
        $response2->assertStatus(400);
    }

    public function test_local_private_file_streaming(): void
    {
        Storage::fake('local');
        $fakeContent = 'SAMPLE_FIRMWARE_BINARY_STREAM_CONTENT';
        Storage::disk('local')->put('private_downloads/firmware/test_rom.zip', $fakeContent);

        $localMirror = $this->firmware->mirrors()->create([
            'mirror_name' => 'Private Server Storage',
            'disk' => 'local',
            'file_path' => 'private_downloads/firmware/test_rom.zip',
            'file_size_bytes' => strlen($fakeContent),
            'is_local' => true,
            'is_active' => true,
        ]);

        $response = $this->get(route('downloads.mirror', $localMirror));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));

        // Verify request logged with streamed status
        $this->assertDatabaseHas('download_requests', [
            'mirror_id' => $localMirror->id,
            'status' => 'streamed',
        ]);
    }

    public function test_rate_limiting_throttle_is_configured_on_download_route(): void
    {
        $routes = Route::getRoutes();
        $downloadRoute = $routes->getByName('downloads.mirror');

        $this->assertNotNull($downloadRoute);
        $middleware = $downloadRoute->gatherMiddleware();

        $hasThrottle = false;
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'throttle:')) {
                $hasThrottle = true;
                break;
            }
        }

        $this->assertTrue($hasThrottle, 'Download gateway route must be protected by a throttle middleware.');
    }
}
