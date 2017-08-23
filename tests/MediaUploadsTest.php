<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Setting;
use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Http\UploadedFile;
use Faker\Factory as Faker;

class MediaUploadsTest extends TestCase
{
    protected $file;

    public function setUp()
    {
        parent::setUp();

        $file_path = Faker::create()->file(__DIR__ . '/tmp/src', __DIR__ . '/tmp/dest');

        $this->file = new UploadedFile($file_path, 'test_file.txt', null, null, null, true);
    }

    /** @test */
    public function it_can_add_media()
    {
        $setting = factory(Setting::class)->create();

        $setting->addMedia($this->file)->toMediaCollection();

        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_can_register_a_new_setting_file_upload()
    {
        $user = factory(User::class)->create();

        $setting = Setting::register('upload', $this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
        $this->assertCount(1, $setting->getMedia());
    }

    /** @test */
    public function it_can_register_with_a_file_upload_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('scope.name', $this->file, $user);

        $this->assertEquals('name', $setting->name);
        $this->assertEquals('scope', $setting->scope);
        $this->assertEquals('test_file.txt', $setting->value);
    }

    /** @test */
    public function it_can_update_the_setting_with_a_file_upload()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name']);

        $setting->updateValue($this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
    }

    /** @test */
    public function it_can_update_with_a_scope_with_a_file_upload()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'value', 'scope' => 'scope']);

        $setting->updateValue($this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_updates_the_user_id_when_updating_a_setting()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => 'original value']);

        $this->actingAs($user);
        $setting->updateValue($this->file, $user);

        $this->assertEquals($user->id, $setting->updated_by);
    }

    /** @test */
    public function it_sets_the_user_id_when_registering_a_setting()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $setting = Setting::register('name', $this->file, $user);

        $this->assertEquals($user->id, $setting->updated_by);
    }

    /** @test */
    public function it_can_get_the_updated_by_user_id_after_being_updated()
    {
        $user = factory(User::class)->create(['id' => 1]);
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'value']);

        $setting->updateValue($this->file, $user);

        $user_id = Setting::getUpdatedBy('name');
        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_can_get_the_updated_at_timestamp_after_being_updated()
    {
        $user = factory(User::class)->create(['id' => 1]);
        $setting = factory(Setting::class)->create(['name' => 'name']);

        $setting->updateValue($this->file, $user);

        $timestamp = Setting::getWhenUpdated('name');
        $this->assertEquals($setting->updated_at, $timestamp);
    }
}
