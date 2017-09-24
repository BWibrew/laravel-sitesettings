<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Setting;
use Illuminate\Http\UploadedFile;
use BWibrew\SiteSettings\Tests\Models\User;

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
    public function it_adds_media()
    {
        $this->setting->addMedia($this->file)->toMediaCollection();

        $this->assertCount(1, $this->setting->getMedia());
    }

    /** @test */
    public function it_registers_a_file_upload()
    {
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals('logo.png', $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_registers_a_file_upload_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('scope.name', $this->file, $user);

        $this->assertEquals('name', $setting->name);
        $this->assertEquals('scope', $setting->scope);
        $this->assertEquals('logo.png', $setting->value);
    }

    /** @test */
    public function it_updates_with_a_file_upload()
    {
        $user = factory(User::class)->create();

        $this->setting->updateValue($this->file, $user);

        $this->assertEquals('logo.png', $this->setting->value);
    }

    /** @test */
    public function it_updates_with_a_scope_with_a_file_upload()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $this->setting->updateValue($this->file, $user);

        $this->assertEquals('logo.png', $this->setting->value);
        $this->assertEquals($this->setting->scope, $this->setting->scope);
    }

    /** @test */
    public function it_updates_updated_by_with_a_file_upload()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $this->setting->updateValue($this->file, $user);

        $this->assertEquals($user->id, $this->setting->updated_by);
    }

    /** @test */
    public function it_sets_updated_by_when_registering()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $setting = Setting::register('name', $this->file, $user);

        $this->assertEquals($user->id, $setting->updated_by);
    }

    /** @test */
    public function it_gets_updated_by()
    {
        $user = factory(User::class)->create();

        $this->setting->updateValue($this->file, $user);

        $user_id = Setting::getUpdatedBy($this->setting->name);
        $this->assertEquals($user->id, $user_id);
    }

    /** @test */
    public function it_gets_updated_at()
    {
        $user = factory(User::class)->create();

        $this->setting->updateValue($this->file, $user);

        $timestamp = Setting::getUpdatedAt($this->setting->name);
        $this->assertEquals($this->setting->updated_at, $timestamp);
    }

    /** @test */
    public function it_stores_single_file_per_setting()
    {
        $user = factory(User::class)->create();
        $updated = UploadedFile::fake()->image('logo2.png')->size(100);

        $this->setting->updateValue($this->file, $user);
        $this->assertCount(1, $this->setting->getMedia());
        $this->assertEquals('logo.png', $this->setting->getMedia()->first()->file_name);

        $this->setting->updateValue($updated, $user);
        $this->assertCount(1, $this->setting->getMedia());
        $this->assertEquals('logo2.png', $this->setting->getMedia()->first()->file_name);
    }

    /** @test */
    public function it_gets_filename_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'file_name');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals('logo.png', $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_gets_url_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'url');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals($setting->getMedia()->first()->getUrl(), $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_gets_file_path_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'path');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals($setting->getMedia()->first()->getPath(), $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }
}
