<?php

namespace BWibrew\SiteSettings\Tests;

use Faker\Factory as Faker;
use BWibrew\SiteSettings\Setting;
use Illuminate\Http\UploadedFile;
use BWibrew\SiteSettings\Tests\Models\User;

class MediaUploadsTest extends TestCase
{
    protected $file;

    public function setUp()
    {
        parent::setUp();

        $file_path = Faker::create()->file(__DIR__.'/tmp/src', __DIR__.'/tmp/dest');

        $this->file = new UploadedFile($file_path, 'test_file.txt', null, null, null, true);
    }

    /** @test */
    public function it_adds_media()
    {
        $setting = factory(Setting::class)->create();

        $setting->addMedia($this->file)->toMediaCollection();

        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_registers_a_file_upload()
    {
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
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
        $this->assertEquals('test_file.txt', $setting->value);
    }

    /** @test */
    public function it_updates_with_a_file_upload()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name']);

        $setting->updateValue($this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
    }

    /** @test */
    public function it_updates_with_a_scope_with_a_file_upload()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'value', 'scope' => 'scope']);

        $setting->updateValue($this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_updates_updated_by_with_a_file_upload()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => 'original value']);

        $this->actingAs($user);
        $setting->updateValue($this->file, $user);

        $this->assertEquals($user->id, $setting->updated_by);
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
        $user = factory(User::class)->create(['id' => 1]);
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'value']);

        $setting->updateValue($this->file, $user);

        $user_id = Setting::getUpdatedBy('name');
        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_gets_updated_at()
    {
        $user = factory(User::class)->create(['id' => 1]);
        $setting = factory(Setting::class)->create(['name' => 'name']);

        $setting->updateValue($this->file, $user);

        $timestamp = Setting::getWhenUpdated('name');
        $this->assertEquals($setting->updated_at, $timestamp);
    }

    /** @test */
    public function it_stores_single_file_per_setting()
    {
        $user = factory(User::class)->create();
        $another_file = new UploadedFile(
            Faker::create()->file(__DIR__.'/tmp/src', __DIR__.'/tmp/dest'),
            'test_file.txt',
            null,
            null,
            null,
            true
        );

        $setting = factory(Setting::class)->create()->updateValue($this->file, $user);

        $this->assertCount(1, $setting->getMedia());

        $setting->updateValue($another_file, $user);

        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_gets_filename_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'file_name');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_gets_url_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'url');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals($setting->getMedia()[0]->getUrl(), $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_gets_file_path_when_set_in_config()
    {
        $this->app['config']->set('sitesettings.media_value_type', 'path');
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals($setting->getMedia()[0]->getPath(), $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }
}
