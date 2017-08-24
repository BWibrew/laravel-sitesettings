<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Setting;
use BWibrew\SiteSettings\Tests\Models\User;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->withFactories(__DIR__.'/../src/database/factories');
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
        $app['config']->set('sitesettings.force_naming_style', true);
        $app['config']->set('sitesettings.naming_styles', [
            'snake_case',
        ]);
        $app['config']->set('sitesettings.use_scopes', true);
        $app['config']->set('sitesettings.media_value_type', 'file_name');
        $app['config']->set('filesystems.disks.media', [
            'driver' => 'local',
            'root'   => public_path('media'),
        ]);

        include_once __DIR__ . '/migrations/create_media_table.php.stub';
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

    /**
     * Test running migration.
     *
     * @test
     */
    public function test_migrations_run()
    {
        $setting = Setting::create([
            'name' => 'setting_name',
            'value' => 'a setting value'
        ]);

        $this->assertEquals('setting_name', $setting->name);
        $this->assertEquals('a setting value', $setting->value);

        $settings = \DB::table('settings')->where('id', '=', 1)->first();
        $this->assertEquals('setting_name', $settings->name);
        $this->assertEquals('a setting value', $settings->value);
    }
}
