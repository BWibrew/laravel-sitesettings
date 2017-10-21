<?php

namespace BWibrew\SiteSettings;

use Illuminate\Support\ServiceProvider;

class SiteSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations'));

        $this->publishes([
            realpath(__DIR__.'/../config/sitesettings.php') => config_path('sitesettings.php'),
        ], 'config');

        $this->publishes([
            realpath(__DIR__.'/../database/migrations/') => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Spatie\MediaLibrary\MediaLibraryServiceProvider::class);
    }
}
