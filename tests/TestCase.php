<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;
use BWibrew\SiteSettings\Tests\Models\SettingWithMedia;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/database/factories');
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

        // Migrations
        $this->migrateSettingsTable();
        $this->migrateMediaTable();
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

    protected function migrateSettingsTable()
    {
        include_once __DIR__.'/../database/migrations/create_settings_table.php.stub';
        (new \CreateSettingsTable())->up();
    }

    protected function migrateMediaTable()
    {
        include_once __DIR__ . '/database/migrations/create_media_table.php.stub';
        (new \CreateMediaTable())->up();
    }
}
