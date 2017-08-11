<?php

namespace Jamin87\SiteSettings\Tests;

use Jamin87\SiteSettings\Setting;
use Jamin87\SiteSettings\Tests\Models\User;

class ScopeTest extends TestCase
{
    /** @test */
    public function it_has_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['scope' => 'scope_name']);

        $this->assertEquals($setting->scope, 'scope_name');
    }

    /** @test */
    public function it_can_register_a_new_setting_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', null, $user);

        $this->assertEquals($setting->name, 'registered_setting');
        $this->assertEquals($setting->scope, 'registered_scope');
    }

    /** @test */
    public function it_can_register_a_new_setting_with_a_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', 'registered_value', $user);

        $this->assertEquals($setting->name, 'registered_setting');
        $this->assertEquals($setting->scope, 'registered_scope');
        $this->assertEquals($setting->value, 'registered_value');
    }

    /** @test */
    public function it_can_register_with_a_scope_with_multiple_items_in_dot_syntax()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('scope.name.other.item', null, $user);

        $this->assertEquals($setting->name, 'name.other.item');
        $this->assertEquals($setting->scope, 'scope');
    }

    /** @test */
    public function it_cannot_register_a_new_setting_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', null, $user);

        $this->assertEquals($setting->name, 'registered_scope.registered_setting');
        $this->assertEquals($setting->scope, null);
    }

    /** @test */
    public function it_can_update_the_setting_name_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'scope' => null]);

        $setting->updateName('scope.new_name');

        $this->assertEquals('new_name', $setting->name);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_cannot_update_the_setting_name_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'scope' => null]);

        $setting->updateName('scope.new_name');

        $this->assertEquals('scope.new_name', $setting->name);
        $this->assertEquals(null, $setting->scope);
    }
}
