<?php

namespace BWibrew\SiteSettings\Tests\Scoped;

use BWibrew\SiteSettings\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use BWibrew\SiteSettings\Tests\Models\User;
use BWibrew\SiteSettings\Models\SettingWithMedia as Setting;

class MediaUploadsTest extends TestCase
{
    protected $setting;
    protected $file;

    public function setUp()
    {
        parent::setUp();

        $this->setting = factory(Setting::class)->create();
        $this->file = UploadedFile::fake()->image('logo.png')->size(100);
    }

    /** @test */
    public function it_registers_a_file_upload_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('scope.name', $this->file);

        $this->assertEquals('name', $setting->name);
        $this->assertEquals('scope', $setting->scope->name);
        $this->assertEquals('logo.png', $setting->value);
        $this->assertCount(1, $setting->getMedia());

        Storage::disk('public')->assertExists($this->getStoragePath($setting->id));
    }

    /** @test */
    public function it_updates_with_a_scope_with_a_file_upload()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $this->setting->updateValue($this->file);

        $this->assertEquals('logo.png', $this->setting->value);
        $this->assertEquals($this->setting->scope, $this->setting->scope);

        Storage::disk('public')->assertExists($this->getStoragePath($this->setting->id));
    }
}
