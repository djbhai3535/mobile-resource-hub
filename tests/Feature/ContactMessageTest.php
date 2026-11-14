<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactMessageTest extends TestCase
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

    public function test_contact_form_persists_to_database(): void
    {
        $response = $this->post('/contact-us', [
            'name' => 'Michael Chang',
            'email' => 'mchang@firmware-repair.org',
            'subject' => 'Request for Xiaomi HyperOS Scatter File',
            'message' => 'Please upload the official scatter partition mapping for Redmi Note 13.',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Michael Chang',
            'email' => 'mchang@firmware-repair.org',
            'subject' => 'Request for Xiaomi HyperOS Scatter File',
            'status' => 'unread',
        ]);
    }

    public function test_honeypot_prevents_db_persistence_for_bots(): void
    {
        $response = $this->post('/contact-us', [
            'name' => 'Automated Bot',
            'email' => 'bot@spammer.net',
            'subject' => 'Buy Crypto Now',
            'message' => 'Spam payload',
            'mrh_website_hp' => 'I am a spambot',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'bot@spammer.net',
        ]);
    }

    public function test_admin_can_view_and_toggle_read_status(): void
    {
        $message = ContactMessage::create([
            'name' => 'David Miller',
            'email' => 'dmiller@tech.com',
            'subject' => 'Broken Download Link',
            'message' => 'Mirror 2 is reporting timeout.',
            'status' => 'unread',
        ]);

        // Viewing detail marks as read automatically
        $response = $this->actingAs($this->admin)->get("/admin/messages/{$message->id}");
        $response->assertStatus(200);
        $this->assertEquals('read', $message->fresh()->status);

        // Toggle back to unread
        $toggleResponse = $this->actingAs($this->admin)->post("/admin/messages/{$message->id}/toggle-read");
        $toggleResponse->assertSessionHas('success');
        $this->assertEquals('unread', $message->fresh()->status);
    }

    public function test_admin_can_delete_inquiry(): void
    {
        $message = ContactMessage::create([
            'name' => 'Spam Test',
            'email' => 'spam@test.com',
            'subject' => 'Discard Me',
            'message' => 'Content',
            'status' => 'unread',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/messages/{$message->id}");
        $response->assertRedirect('/admin/messages');
        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }
}
