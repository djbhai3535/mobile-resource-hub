<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->seed(AdminUserSeeder::class);
        $this->seed(\Database\Seeders\ContentSeeder::class);

        $this->admin = User::where('email', 'admin@mobilehub.local')->first();
        $this->category = Category::where('slug', 'smartphone-news')->first() ?? Category::first();
    }

    public function test_admin_can_view_posts_index_and_filter(): void
    {
        Post::create([
            'author_id' => $this->admin->id,
            'title' => 'Article One',
            'slug' => 'article-one',
            'content' => 'Content of article one',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/posts');
        $response->assertStatus(200);
        $response->assertSee('Article One');
    }

    public function test_post_creation_with_validation_and_slug_generation(): void
    {
        $tag = Tag::create(['name' => 'Snapdragon', 'slug' => 'snapdragon']);

        $response = $this->actingAs($this->admin)->post('/admin/posts', [
            'title' => 'Snapdragon 8 Elite Announced',
            'content' => '<p>Qualcomm announces new Oryon CPU architecture for flagship mobile devices.</p>',
            'category_id' => $this->category->id,
            'type' => 'news',
            'status' => 'published',
            'tags' => [$tag->id],
        ]);

        $response->assertRedirect('/admin/posts');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('posts', [
            'title' => 'Snapdragon 8 Elite Announced',
            'slug' => 'snapdragon-8-elite-announced',
            'status' => 'published',
        ]);

        $post = Post::where('slug', 'snapdragon-8-elite-announced')->first();
        $this->assertTrue($post->tags->contains($tag));
    }

    public function test_post_update_persists_changes(): void
    {
        $post = Post::create([
            'author_id' => $this->admin->id,
            'title' => 'Original Title',
            'slug' => 'original-title',
            'content' => 'Initial content',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/posts/{$post->id}", [
            'title' => 'Updated Title Milestone',
            'slug' => 'updated-title-milestone',
            'content' => 'Updated content body',
            'type' => 'article',
            'status' => 'published',
        ]);

        $response->assertRedirect('/admin/posts');
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title Milestone',
            'status' => 'published',
        ]);
    }

    public function test_post_publishing_and_unpublishing(): void
    {
        $post = Post::create([
            'author_id' => $this->admin->id,
            'title' => 'Pending Publication',
            'slug' => 'pending-publication',
            'content' => 'Review content',
            'status' => 'draft',
        ]);

        // Publish
        $this->actingAs($this->admin)->post("/admin/posts/{$post->id}/publish");
        $this->assertEquals('published', $post->fresh()->status);
        $this->assertNotNull($post->fresh()->published_at);

        // Unpublish
        $this->actingAs($this->admin)->post("/admin/posts/{$post->id}/unpublish");
        $this->assertEquals('draft', $post->fresh()->status);
    }

    public function test_post_soft_delete_and_restore(): void
    {
        $post = Post::create([
            'author_id' => $this->admin->id,
            'title' => 'Post to Delete',
            'slug' => 'post-to-delete',
            'content' => 'To be trashed',
            'status' => 'draft',
        ]);

        // Soft delete
        $this->actingAs($this->admin)->delete("/admin/posts/{$post->id}");
        $this->assertSoftDeleted('posts', ['id' => $post->id]);

        // Restore
        $this->actingAs($this->admin)->post("/admin/posts/{$post->id}/restore");
        $this->assertNotSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_published_post_visible_on_public_site(): void
    {
        $post = Post::create([
            'author_id' => $this->admin->id,
            'category_id' => $this->category->id,
            'title' => 'Public Visibility Live Post',
            'slug' => 'public-visibility-live-post',
            'excerpt' => 'This is the excerpt of the public post.',
            'content' => 'This article is fully published and visible.',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertSee('Public Visibility Live Post');

        $detail = $this->get('/news/public-visibility-live-post');
        $detail->assertStatus(200);
        $detail->assertSee('This article is fully published and visible.');
    }

    public function test_draft_post_returns_404_on_public_site(): void
    {
        Post::create([
            'author_id' => $this->admin->id,
            'title' => 'Hidden Secret Draft',
            'slug' => 'hidden-secret-draft',
            'content' => 'Unpublished confidential draft.',
            'status' => 'draft',
        ]);

        $response = $this->get('/news/hidden-secret-draft');
        $response->assertStatus(404);
    }
}
