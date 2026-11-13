<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicRoutesTest extends TestCase
{
    public function test_homepage_renders_successfully_with_all_core_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Mobile<span class="text-primary">Hub</span>', false);
        $response->assertSee('Search Specs, Firmware, &', false);
        $response->assertSee('Browse by Brand');
        $response->assertSee('Latest Mobile Devices');
        $response->assertSee('Recent Firmware & Flash Files', false);
        $response->assertSee('Essential Flash Tools');
        $response->assertSee('Official USB Drivers');
        $response->assertSee('Guides, Fixes & Tech News', false);
        $response->assertSee('Advertisement Slot');
        $response->assertSee('Privacy Policy');
        $response->assertSee('Terms of Service');
        $response->assertSee('DMCA & Copyright', false);
    }

    public function test_device_directory_and_detail_render(): void
    {
        $dirResponse = $this->get('/mobile-phones');
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('Smartphone Catalog');
        $dirResponse->assertSee('Galaxy S24 Ultra');

        $detailResponse = $this->get('/mobile-phones/galaxy-s24-ultra');
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Samsung Galaxy S24 Ultra');
        $detailResponse->assertSee('Technical Specifications');
        $detailResponse->assertSee('Qualcomm SM8650-AC Snapdragon 8 Gen 3');
        $detailResponse->assertSee('Dynamic LTPO AMOLED 2X');
        $detailResponse->assertSee('Compatible Firmware');
    }

    public function test_brands_directory_and_detail_render(): void
    {
        $dirResponse = $this->get('/brands');
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('Mobile Brand Directory');
        $dirResponse->assertSee('Samsung');

        $detailResponse = $this->get('/brands/samsung');
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Samsung Smartphones & Resources', false);
    }

    public function test_firmware_directory_and_detail_render(): void
    {
        $dirResponse = $this->get('/firmware');
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('Firmware & Stock ROM Hub', false);

        $detailResponse = $this->get('/firmware/galaxy-s24-ultra-sm-s928b-oneui7');
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Galaxy S24 Ultra Android 15 OneUI 7.0');
        $detailResponse->assertSee('S928BXXU3BXK2');
        $detailResponse->assertSee('SHA-256 Hash');
        $detailResponse->assertSee('Flashing Prerequisites & Instructions', false);
    }

    public function test_flash_files_directory_and_detail_render(): void
    {
        $dirResponse = $this->get('/flash-files');
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('Flash File Resource Hub');

        $detailResponse = $this->get('/flash-files/redmi-note-12-4g-scatter-rom');
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Official Scatter File');
        $detailResponse->assertSee('MT6785_Android_scatter.txt');
    }

    public function test_software_and_drivers_render(): void
    {
        $softDir = $this->get('/software');
        $softDir->assertStatus(200);
        $softDir->assertSee('Samsung Odin 3.14.4');

        $softDetail = $this->get('/software/odin-3-14');
        $softDetail->assertStatus(200);
        $softDetail->assertSee('Samsung Odin 3.14.4');

        $driverDir = $this->get('/drivers');
        $driverDir->assertStatus(200);
        $driverDir->assertSee('Official Mobile USB Drivers');

        $driverDetail = $this->get('/drivers/qualcomm-usb-driver');
        $driverDetail->assertStatus(200);
        $driverDetail->assertSee('Qualcomm HS-USB QDLoader 9008 Driver');
    }

    public function test_news_and_tutorials_render(): void
    {
        $newsDir = $this->get('/news');
        $newsDir->assertStatus(200);
        $newsDir->assertSee('Mobile Technology News');

        $newsDetail = $this->get('/news/samsung-oneui-7-global-rollout-schedule');
        $newsDetail->assertStatus(200);
        $newsDetail->assertSee('Samsung Officially Begins One UI 7.0 Android 15 Rollout');

        $tutDir = $this->get('/tutorials');
        $tutDir->assertStatus(200);
        $tutDir->assertSee('Flashing & Repair Tutorials', false);

        $tutDetail = $this->get('/tutorials/how-to-flash-samsung-firmware-using-odin');
        $tutDetail->assertStatus(200);
        $tutDetail->assertSee('How to Flash Official Samsung Firmware Using Odin 3.14');
    }

    public function test_search_page_and_suggestions_api(): void
    {
        $searchPage = $this->get('/search?q=Galaxy');
        $searchPage->assertStatus(200);
        $searchPage->assertSee('Results for');
        $searchPage->assertSee('Galaxy');

        $emptySearch = $this->get('/search');
        $emptySearch->assertStatus(200);
        $emptySearch->assertSee('Global Catalog Search');

        $apiResponse = $this->get('/api/search/suggestions?q=samsung');
        $apiResponse->assertStatus(200);
        $apiResponse->assertJsonStructure([
            'devices',
            'firmware',
            'software',
            'posts',
        ]);
    }

    public function test_static_and_legal_pages_render(): void
    {
        $this->get('/about-us')->assertStatus(200)->assertSee('About Mobile Resource Hub');
        $this->get('/contact-us')->assertStatus(200)->assertSee('Get in Touch with Us');
        $this->get('/privacy-policy')->assertStatus(200)->assertSee('Privacy Policy');
        $this->get('/terms-and-conditions')->assertStatus(200)->assertSee('Terms and Conditions');
        $this->get('/dmca')->assertStatus(200)->assertSee('DMCA & Copyright Notice', false);
    }

    public function test_contact_form_submission_validation(): void
    {
        // Valid submission
        $validResponse = $this->post('/contact-us', [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'General Inquiry',
            'message' => 'Testing contact submission form.',
        ]);
        $validResponse->assertSessionHas('success');

        // Validation failure
        $invalidResponse = $this->post('/contact-us', [
            'name' => '',
            'email' => 'invalid-email',
            'subject' => '',
            'message' => '',
        ]);
        $invalidResponse->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
    }
}
