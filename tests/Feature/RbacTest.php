<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
    }

    public function test_super_admin_has_unrestricted_access(): void
    {
        $superAdmin = User::where('email', 'admin@mobilehub.local')->first();

        $this->actingAs($superAdmin);

        $this->assertTrue(Gate::allows('viewAny', Post::class));
        $this->assertTrue(Gate::allows('create', Post::class));

        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_editor_can_publish_and_edit_posts(): void
    {
        $editor = User::where('email', 'editor@mobilehub.local')->first();
        $author = User::where('email', 'author@mobilehub.local')->first();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Author Draft Post',
            'slug' => 'author-draft-post',
            'content' => 'Content here',
            'status' => 'draft',
        ]);

        $this->actingAs($editor);

        $this->assertTrue(Gate::allows('publish', $post));
        $this->assertTrue(Gate::allows('update', $post));

        $publishResponse = $this->post("/admin/posts/{$post->id}/publish");
        $publishResponse->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'published',
        ]);
    }

    public function test_author_cannot_publish_post_directly(): void
    {
        $author = User::where('email', 'author@mobilehub.local')->first();

        $post = Post::create([
            'author_id' => $author->id,
            'title' => 'Author Direct Publish Attempt',
            'slug' => 'author-direct-publish-attempt',
            'content' => 'Content here',
            'status' => 'draft',
        ]);

        $this->actingAs($author);

        $this->assertFalse(Gate::allows('publish', $post));

        $response = $this->post("/admin/posts/{$post->id}/publish");
        $response->assertStatus(403);
    }

    public function test_author_cannot_edit_other_author_posts(): void
    {
        $author1 = User::where('email', 'author@mobilehub.local')->first();
        $editor = User::where('email', 'editor@mobilehub.local')->first();

        $editorPost = Post::create([
            'author_id' => $editor->id,
            'title' => 'Editor Exclusive Post',
            'slug' => 'editor-exclusive-post',
            'content' => 'Editor content',
            'status' => 'draft',
        ]);

        $this->actingAs($author1);

        $this->assertFalse(Gate::allows('update', $editorPost));

        $response = $this->put("/admin/posts/{$editorPost->id}", [
            'title' => 'Hacked Title',
            'slug' => 'editor-exclusive-post',
            'content' => 'Tampered content',
            'type' => 'news',
            'status' => 'draft',
        ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_protected_admin_routes(): void
    {
        $response = $this->get('/admin/activity-logs');
        $response->assertRedirect('/admin/login');
    }
}
