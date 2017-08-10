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

        $setting = new Setting(['name' => 'setting_name']);
        $this->assertEquals($setting->name, 'setting_name');

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }

    /** @test */
    public function it_enforces_camel_case_when_enabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', ['camel_case']);

        $setting = new Setting(['name' => 'settingName']);
        $this->assertEquals($setting->name, 'settingName');

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }

    /** @test */
    public function it_enforces_kebab_case_when_enabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', ['kebab_case']);

        $setting = new Setting(['name' => 'setting-name']);
        $this->assertEquals($setting->name, 'setting-name');

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }

    /** @test */
    public function it_enforces_studly_case_when_enabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', ['studly_case']);

        $setting = new Setting(['name' => 'SettingName']);
        $this->assertEquals($setting->name, 'SettingName');

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }

    /** @test */
    public function it_enforces_multiple_naming_styles()
    {
        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', [
            'snake_case', 'camel_case', 'kebab_case', 'studly_case'
        ]);

        $setting = new Setting(['name' => 'setting_name']);
        $this->assertEquals($setting->name, 'setting_name');

        $setting = new Setting(['name' => 'settingName']);
        $this->assertEquals($setting->name, 'settingName');

        $setting = new Setting(['name' => 'setting-name']);
        $this->assertEquals($setting->name, 'setting-name');

        $setting = new Setting(['name' => 'SettingName']);
        $this->assertEquals($setting->name, 'SettingName');

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }

    /** @test */
    public function it_does_not_enforce_naming_style_when_disabled_in_config()
    {
        $this->app['config']->set('sitesettings.force_naming_style', false);

        $setting = new Setting(['name' => 'Setting Name']);

        $this->assertEquals($setting->name, 'Setting Name');

        $this->app['config']->set('sitesettings.force_naming_style', true);
        $this->app['config']->set('sitesettings.naming_styles', ['snake_case']);

        $this->expectException('Exception');
        new Setting(['name' => 'Setting Name']);
    }
}
