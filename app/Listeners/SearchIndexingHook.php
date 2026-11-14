<?php

namespace App\Listeners;

use App\Events\BrandDeleted;
use App\Events\BrandSaved;
use App\Events\DeviceDeleted;
use App\Events\DeviceSaved;
use App\Events\DriverDeleted;
use App\Events\DriverSaved;
use App\Events\FirmwareDeleted;
use App\Events\FirmwareSaved;
use App\Events\FlashFileDeleted;
use App\Events\FlashFileSaved;
use App\Events\PageSaved;
use App\Events\PostDeleted;
use App\Events\PostSaved;
use App\Events\SoftwareDeleted;
use App\Events\SoftwareSaved;
use App\Services\SearchIndexingService;
use Illuminate\Support\Facades\Log;

class SearchIndexingHook
{
    public function __construct(
        protected SearchIndexingService $indexingService
    ) {}

    /**
     * Handle Post Saved events for SearchService indexing.
     */
    public function handlePostSaved(PostSaved $event): void
    {
        try {
            $this->indexingService->sync($event->post);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on PostSaved: ' . $e->getMessage(), ['post_id' => $event->post->id]);
        }
    }

    /**
     * Handle Post Deleted events for SearchService removal.
     */
    public function handlePostDeleted(PostDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->post);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on PostDeleted: ' . $e->getMessage(), ['post_id' => $event->post->id]);
        }
    }

    /**
     * Handle Page Saved events for SearchService indexing.
     */
    public function handlePageSaved(PageSaved $event): void
    {
        try {
            $this->indexingService->sync($event->page);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on PageSaved: ' . $e->getMessage(), ['page_id' => $event->page->id]);
        }
    }

    /**
     * Handle Brand Saved events.
     */
    public function handleBrandSaved(BrandSaved $event): void
    {
        try {
            $this->indexingService->sync($event->brand);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on BrandSaved: ' . $e->getMessage(), ['brand_id' => $event->brand->id]);
        }
    }

    /**
     * Handle Brand Deleted events.
     */
    public function handleBrandDeleted(BrandDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->brand);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on BrandDeleted: ' . $e->getMessage(), ['brand_id' => $event->brand->id]);
        }
    }

    /**
     * Handle Device Saved events.
     */
    public function handleDeviceSaved(DeviceSaved $event): void
    {
        try {
            $this->indexingService->sync($event->device);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on DeviceSaved: ' . $e->getMessage(), ['device_id' => $event->device->id]);
        }
    }

    /**
     * Handle Device Deleted events.
     */
    public function handleDeviceDeleted(DeviceDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->device);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on DeviceDeleted: ' . $e->getMessage(), ['device_id' => $event->device->id]);
        }
    }

    /**
     * Handle Firmware Saved events.
     */
    public function handleFirmwareSaved(FirmwareSaved $event): void
    {
        try {
            $this->indexingService->sync($event->firmware);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on FirmwareSaved: ' . $e->getMessage(), ['firmware_id' => $event->firmware->id]);
        }
    }

    /**
     * Handle Firmware Deleted events.
     */
    public function handleFirmwareDeleted(FirmwareDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->firmware);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on FirmwareDeleted: ' . $e->getMessage(), ['firmware_id' => $event->firmware->id]);
        }
    }

    /**
     * Handle Flash File Saved events.
     */
    public function handleFlashFileSaved(FlashFileSaved $event): void
    {
        try {
            $this->indexingService->sync($event->flashFile);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on FlashFileSaved: ' . $e->getMessage(), ['flash_file_id' => $event->flashFile->id]);
        }
    }

    /**
     * Handle Flash File Deleted events.
     */
    public function handleFlashFileDeleted(FlashFileDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->flashFile);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on FlashFileDeleted: ' . $e->getMessage(), ['flash_file_id' => $event->flashFile->id]);
        }
    }

    /**
     * Handle Software Saved events.
     */
    public function handleSoftwareSaved(SoftwareSaved $event): void
    {
        try {
            $this->indexingService->sync($event->software);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on SoftwareSaved: ' . $e->getMessage(), ['software_id' => $event->software->id]);
        }
    }

    /**
     * Handle Software Deleted events.
     */
    public function handleSoftwareDeleted(SoftwareDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->software);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on SoftwareDeleted: ' . $e->getMessage(), ['software_id' => $event->software->id]);
        }
    }

    /**
     * Handle Driver Saved events.
     */
    public function handleDriverSaved(DriverSaved $event): void
    {
        try {
            $this->indexingService->sync($event->driver);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on DriverSaved: ' . $e->getMessage(), ['driver_id' => $event->driver->id]);
        }
    }

    /**
     * Handle Driver Deleted events.
     */
    public function handleDriverDeleted(DriverDeleted $event): void
    {
        try {
            $this->indexingService->remove($event->driver);
        } catch (\Throwable $e) {
            Log::error('SearchIndexingHook failed on DriverDeleted: ' . $e->getMessage(), ['driver_id' => $event->driver->id]);
        }
    }

    /**
     * Register listeners for subscriber.
     */
    public function subscribe(): array
    {
        return [
            PostSaved::class => 'handlePostSaved',
            PostDeleted::class => 'handlePostDeleted',
            PageSaved::class => 'handlePageSaved',
            BrandSaved::class => 'handleBrandSaved',
            BrandDeleted::class => 'handleBrandDeleted',
            DeviceSaved::class => 'handleDeviceSaved',
            DeviceDeleted::class => 'handleDeviceDeleted',
            FirmwareSaved::class => 'handleFirmwareSaved',
            FirmwareDeleted::class => 'handleFirmwareDeleted',
            FlashFileSaved::class => 'handleFlashFileSaved',
            FlashFileDeleted::class => 'handleFlashFileDeleted',
            SoftwareSaved::class => 'handleSoftwareSaved',
            SoftwareDeleted::class => 'handleSoftwareDeleted',
            DriverSaved::class => 'handleDriverSaved',
            DriverDeleted::class => 'handleDriverDeleted',
        ];
    }
}
