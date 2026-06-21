<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Pagination\Paginator;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Azure\AzureExtendSocialite;
use Illuminate\Support\Facades\URL; // <-- 1. Tambahkan import URL di sini

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
        Paginator::useBootstrapFive();
        Event::listen(SocialiteWasCalled::class, AzureExtendSocialite::class);

        // <-- 2. Tambahkan pengecekan HTTPS di sini -->
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
