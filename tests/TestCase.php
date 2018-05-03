<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Orchestra\Testbench\TestCase as Orchestra;
use BWibrew\SiteSettings\Tests\Models\SettingWithMedia;

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
}
