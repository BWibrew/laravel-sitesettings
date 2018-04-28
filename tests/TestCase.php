<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Tests\Models\User;
use BWibrew\SiteSettings\Tests\Models\Setting;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
        $this->artisan('migrate', ['--database' => 'sqlite']);
        $this->loadLaravelMigrations(['--database' => 'sqlite']);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('sitesettings.use_scopes', true);
        $app['config']->set('sitesettings.media_value_type', 'file_name');
        $app['config']->set('medialibrary.custom_url_generator_class', TestUrlGenerator::class);
        $app['config']->set('medialibrary.defaultFilesystem', 'public');

        include_once __DIR__.'/database/migrations/create_media_table.php.stub';
        (new \CreateMediaTable())->up();
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
        ];
    }

    protected function getStoragePath(int $id)
    {
        $parts = explode('/', Setting::find($id)->getMedia()->first()->getPath());

        return implode('/', array_slice($parts, -2, 2));
    }
}
