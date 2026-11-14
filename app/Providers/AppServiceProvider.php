<?php

namespace App\Providers;

use App\Listeners\SearchIndexingHook;
use App\Models\Brand;
use App\Models\Device;
use App\Models\Post;
use App\Models\User;
use App\Policies\BrandPolicy;
use App\Policies\DevicePolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Resilient default string length for MySQL utf8mb4 indexes
        Schema::defaultStringLength(191);

        // Super Admin gate bypass
        Gate::before(function (User $user, string $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Register Policies
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Device::class, DevicePolicy::class);

        // Register Search Indexing Event Subscriber
        Event::subscribe(SearchIndexingHook::class);

        // Blade @ad('placement') directive
        \Illuminate\Support\Facades\Blade::directive('ad', function ($expression) {
            return "<?php echo app('App\\Services\\AdvertisementService')->render({$expression}); ?>";
        });
    }
}
