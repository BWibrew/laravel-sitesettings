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

        $this->assertEquals('scope_name', $setting->scope);
    }

    /** @test */
    public function it_can_register_a_new_setting_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', null, $user);

        $this->assertEquals('registered_setting', $setting->name);
        $this->assertEquals('registered_scope', $setting->scope);
    }

    /** @test */
    public function it_can_register_a_new_setting_with_a_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', 'registered_value', $user);

        $this->assertEquals('registered_setting', $setting->name);
        $this->assertEquals('registered_scope', $setting->scope);
        $this->assertEquals('registered_value', $setting->value);
    }

    /** @test */
    public function it_can_register_with_a_scope_with_multiple_items_in_dot_syntax()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();

        $setting = Setting::register('scope.name.other.item', null, $user);

        $this->assertEquals('name.other.item', $setting->name);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_cannot_register_a_new_setting_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_scope.registered_setting', null, $user);

        $this->assertEquals('registered_scope.registered_setting', $setting->name);
        $this->assertEquals(null, $setting->scope);
    }

    /** @test */
    public function it_can_update_the_setting_name_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'scope' => null]);

        $setting->updateName('scope.new_name', $user);

        $this->assertEquals('new_name', $setting->name);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_cannot_update_the_setting_name_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'scope' => null]);

        $setting->updateName('scope.new_name', $user);

        $this->assertEquals('scope.new_name', $setting->name);
        $this->assertEquals(null, $setting->scope);
    }

    /** @test */
    public function it_can_update_the_setting_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create([
            'name' => 'name', 'value' => 'original value', 'scope' => 'scope'
        ]);

        $setting->updateValue('new value', $user);

        $this->assertEquals('new value', $setting->value);
        $this->assertEquals('scope', $setting->scope);
    }

    /** @test */
    public function it_can_get_the_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class)->create(['name' => 'name', 'scope' => 'scope', 'value' => 'value']);

        $value = Setting::getValue('scope.name');

        $this->assertEquals('value', $value);
    }

    /** @test */
    public function it_cannot_get_the_value_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class)->create(['name' => 'setting.name', 'scope' => null, 'value' => 'value']);

        $value = Setting::getValue('setting.name');

        $this->assertEquals('value', $value);
    }

    /** @test */
    public function it_can_get_the_updated_by_user_id_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class)->create([
            'name' => 'name', 'scope' => 'scope', 'value' => 'value', 'updated_by' => 1
        ]);

        $user_id = Setting::getUpdatedBy('scope.name');

        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_cannot_get_the_updated_by_user_id_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class)->create([
            'name' => 'setting.name', 'scope' => null, 'value' => 'value', 'updated_by' => 1
        ]);

        $user_id = Setting::getUpdatedBy('setting.name');

        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_can_get_the_updated_timestamp_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['name' => 'name', 'scope' => 'scope']);

        $timestamp = Setting::getWhenUpdated('scope.name');

        $this->assertEquals($setting->updated_at, $timestamp);
    }

    /** @test */
    public function it_cannot_get_the_updated_timestamp_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $setting = factory(Setting::class)->create(['name' => 'setting.name', 'scope' => null]);

        $timestamp = Setting::getWhenUpdated('setting.name');

        $this->assertEquals($setting->updated_at, $timestamp);
    }
}
