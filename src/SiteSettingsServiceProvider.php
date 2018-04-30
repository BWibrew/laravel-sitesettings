<?php

namespace BWibrew\SiteSettings;

use Illuminate\Support\ServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class SiteSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            realpath(__DIR__.'/../config/sitesettings.php') => config_path('sitesettings.php'),
        ], 'config');

        // Hat tip: https://github.com/spatie/laravel-medialibrary/blob/cfc60632369eb18b9585ac7eff33f59b5f7d2507/src/MediaLibraryServiceProvider.php#L21
        if (! class_exists('CreateSettingsTable')) {
            $this->publishes([
                realpath(__DIR__.'/../database/migrations/create_settings_table.php.stub')
                => database_path('migrations/'.date('Y_m_d_His', time()).'_create_settings_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(MediaLibraryServiceProvider::class);

        $this->mergeConfigFrom(__DIR__.'/../config/sitesettings.php', 'sitesettings');
    }
}
