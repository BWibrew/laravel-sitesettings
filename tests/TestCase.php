<?php

namespace BWibrew\SiteSettings\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\FileAdder\FileAdder;
use Orchestra\Testbench\TestCase as Orchestra;
use BWibrew\SiteSettings\Models\SettingWithMedia;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends Orchestra
{
    use DatabaseMigrations;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        if (! class_exists('CreateSettingsTable')) {
            $this->artisan(
                'vendor:publish',
                ['--provider' => 'BWibrew\SiteSettings\SiteSettingsServiceProvider', '--tag' => 'migrations']
            );
        }

        $this->withFactories(__DIR__.'/database/factories');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
        $this->artisan('migrate', ['--database' => 'sqlite']);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Env config
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);

        // Site Settings config
        $app['config']->set('sitesettings.use_scopes', true);
        $app['config']->set('sitesettings.media_value_type', 'file_name');

        // Media Library config
        $app['config']->set('medialibrary.custom_url_generator_class', TestUrlGenerator::class);
        $app['config']->set('medialibrary.defaultFilesystem', 'public');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \BWibrew\SiteSettings\SiteSettingsServiceProvider::class,
            \Orchestra\Database\ConsoleServiceProvider::class,
        ];
    }

    /**
     * Returns the relative path of a setting file.
     *
     * @param int $id
     *
     * @return string
     */
    protected function getStoragePath(int $id)
    {
        $parts = explode('/', SettingWithMedia::find($id)->getMedia()->first()->getPath());

        return implode('/', array_slice($parts, -2, 2));
    }

    protected function logDBQueries()
    {
        DB::connection()->enableQueryLog();
    }

    protected function assertCached()
    {
        $this->assertTrue(Cache::has('bwibrew.settings'));
        $this->assertInstanceOf(Collection::class, Cache::get('bwibrew.settings'));
    }

    /**
     * Ensure compatibility with multiple versions of Spatie Media Library.
     *
     * @param FileAdder $fileAdder
     *
     * @throws \Spatie\MediaLibrary\Exceptions\FileCannotBeAdded
     */
    protected function addFileToMediaCollection(FileAdder $fileAdder)
    {
        if (method_exists($fileAdder, 'toMediaCollection')) {
            // spatie/laravel-medialibrary v6
            $fileAdder->toMediaCollection();
        } elseif (method_exists($fileAdder, 'toMediaLibrary')) {
            // spatie/laravel-medialibrary v5
            $fileAdder->toMediaLibrary();
        }
    }
}
