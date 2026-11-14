<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
        Storage::fake('public');
    }

    protected function createValidPngFile(string $name = 'test-image.png'): UploadedFile
    {
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        return UploadedFile::fake()->createWithContent($name, $pngBytes);
    }

    public function test_valid_image_upload_and_preview(): void
    {
        $file = $this->createValidPngFile('phone-render.png');

        $response = $this->actingAs($this->admin)->post('/admin/media/upload', [
            'file' => $file,
            'alt_text' => 'Sample Phone Render',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('media', [
            'alt_text' => 'Sample Phone Render',
            'mime_type' => 'image/png',
        ]);

        $media = Media::latest()->first();
        Storage::disk('public')->assertExists($media->file_path);
    }

    public function test_disallowed_executable_file_type_is_rejected(): void
    {
        $fakeScript = UploadedFile::fake()->createWithContent('malicious.php', '<?php echo "evil"; ?>');

        $response = $this->actingAs($this->admin)->post('/admin/media/upload', [
            'file' => $fakeScript,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

    public function test_svg_upload_is_strictly_prohibited(): void
    {
        $svgFile = UploadedFile::fake()->createWithContent('vector.svg', '<svg xmlns="http://www.w3.org/2000/svg"><circle r="10"/></svg>');

        $response = $this->actingAs($this->admin)->post('/admin/media/upload', [
            'file' => $svgFile,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('media', 0);
    }

    public function test_media_file_deletion_removes_physical_file(): void
    {
        $file = $this->createValidPngFile('test-banner.png');

        $this->actingAs($this->admin)->post('/admin/media/upload', [
            'file' => $file,
        ]);

        $media = Media::latest()->first();
        Storage::disk('public')->assertExists($media->file_path);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete("/admin/media/{$media->id}");
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->file_path);
    }
}
