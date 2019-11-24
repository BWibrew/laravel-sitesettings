<?php

namespace BWibrew\SiteSettings;

use BWibrew\SiteSettings\Interfaces\ScopeInterface;
use BWibrew\SiteSettings\Interfaces\SettingInterface;
use BWibrew\SiteSettings\Models\Scope;
use BWibrew\SiteSettings\Models\Setting;
use BWibrew\SiteSettings\Models\SettingWithMedia;
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
            realpath(__DIR__ . '/../config/sitesettings.php') => config_path('sitesettings.php'),
        ], 'config');

        // Hat tip: https://github.com/spatie/laravel-medialibrary/blob/cfc60632369eb18b9585ac7eff33f59b5f7d2507/src/MediaLibraryServiceProvider.php#L21
        if (! class_exists('CreateSettingTables')) {
            $migrationStub = realpath(__DIR__ . '/../database/migrations/create_setting_tables.php.stub');
            $migration = database_path('migrations/' . date('Y_m_d_His') . '_create_setting_tables.php');

            $this->publishes([
                $migrationStub => $migration,
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
        if (class_exists(MediaLibraryServiceProvider::class)) {
            $this->app->register(MediaLibraryServiceProvider::class);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/sitesettings.php', 'sitesettings');

        $this->app->bind(ScopeInterface::class, Scope::class);

        $this->app->bind(SettingInterface::class, $this->getSettingModel());
    }

    protected function getSettingModel()
    {
        return class_exists(MediaLibraryServiceProvider::class) ? SettingWithMedia::class : Setting::class;
    }
}
