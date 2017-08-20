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
    public function it_can_update_the_setting_with_a_file_upload()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name']);

        $setting->updateValue($this->file, $user);

        $this->assertEquals('test_file.txt', $setting->value);
    }
}
