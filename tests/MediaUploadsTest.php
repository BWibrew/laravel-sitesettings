<?php

namespace Jamin87\SiteSettings\Tests;

use Jamin87\SiteSettings\Setting;
use Faker\Factory as Faker;

class MediaUploadsTest extends TestCase
{
    /** @test */
    public function it_can_add_media()
    {
        $file = Faker::create()->file(__DIR__ . '/tmp/src', __DIR__ . '/tmp/dest');
        $setting = factory(Setting::class)->create();

        $setting->addMedia($file)->toMediaCollection();

        $this->assertCount(1, $setting->getMedia());
    }
}
