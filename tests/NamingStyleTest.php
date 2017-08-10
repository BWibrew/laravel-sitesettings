<?php

namespace Jamin87\SiteSettings\Tests;

use Jamin87\SiteSettings\Setting;

class NamingStyleTest extends TestCase
{
    /** @test */
    public function it_enforces_snake_case_when_enabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', ['snake_case']);

        $this->expectException('Exception');
        new Setting(['name' => 'A Setting Name']);
    }

    /** @test */
    public function it_does_not_naming_style_when_disabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', false);

        $setting = new Setting(['name' => 'A Setting Name']);

        $this->assertEquals($setting->name, 'A Setting Name');
    }
}
