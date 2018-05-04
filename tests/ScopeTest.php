<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Tests\Models\Scope;
use Illuminate\Support\Facades\Auth;
use BWibrew\SiteSettings\Tests\Models\User;
use BWibrew\SiteSettings\Tests\Models\Setting;

class ScopeTest extends TestCase
{
    /** @test */
    public function it_has_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'scope_name'])->id,
        ]);

        $this->assertEquals('scope_name', $setting->scope()->first()->name);
    }

    /** @test */
    public function it_removes_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
        ]);

        $setting->removeScope();

        $this->assertDatabaseHas('settings', ['scope_id' => 0]);
    }

    /** @test */
    public function it_updates_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'original_scope'])->id,
        ]);

        $setting->updateScope('new_scope');

        $this->assertDatabaseHas('settings', [
            'scope_id' => $setting->scope()->where('name', 'new_scope')->first()->id
        ]);
    }

    /** @test */
    public function it_registers_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('registered_scope.registered_setting');

        $this->assertEquals('registered_setting', $setting->name);
        $this->assertEquals('registered_scope', $setting->scope()->first()->name);
    }

    /** @test */
    public function it_registers_with_a_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('registered_scope.registered_setting', 'registered_value');

        $this->assertEquals('registered_setting', $setting->name);
        $this->assertEquals('registered_scope', $setting->scope()->first()->name);
        $this->assertEquals('registered_value', $setting->value);
    }

    /** @test */
    public function it_registers_with_a_scope_with_multiple_items_in_dot_syntax()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('scope.name.other.item');

        $this->assertEquals('name.other.item', $setting->name);
        $this->assertEquals('scope', $setting->scope()->first()->name);
    }

    /** @test */
    public function it_cannot_register_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('registered_scope.registered_setting');

        $this->assertEquals('registered_scope.registered_setting', $setting->name);
        $this->assertEquals(0, $setting->scope_id);
    }

    /** @test */
    public function it_updates_name_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'original_name']);

        $setting->updateName('scope.new_name');

        $this->assertEquals('new_name', $setting->name);
        $this->assertEquals('scope', $setting->scope()->first()->name);
    }

    /** @test */
    public function it_cannot_update_name_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'original_name']);

        $setting->updateName('scope.new_name');

        $this->assertEquals('scope.new_name', $setting->name);
        $this->assertEquals(0, $setting->scope_id);
    }

    /** @test */
    public function it_updates_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create([
            'name' => 'name',
            'value' => 'original value',
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
        ]);

        $setting->updateValue('new value');

        $this->assertEquals('new value', $setting->value);
        $this->assertEquals('scope', $setting->scope()->first()->name);
    }

    /** @test */
    public function it_gets_value_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class)->create(['name' => 'name', 'value' => 'value1']);
        factory(Setting::class)->create([
            'name' => 'name',
            'value' => 'value2',
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id
        ]);

        $value1 = Setting::getValue('name');
        $value2 = Setting::getValue('scope.name');

        $this->assertEquals('value1', $value1);
        $this->assertEquals('value2', $value2);
    }

    /** @test */
    public function it_cannot_get_value_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class)->create(['name' => 'setting.name', 'value' => 'value']);

        $value = Setting::getValue('setting.name');

        $this->assertEquals('value', $value);
    }

    /** @test */
    public function it_gets_updated_by_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class)->create([
            'name' => 'name', 'value' => 'value1', 'updated_by' => 1,
        ]);
        factory(Setting::class)->create([
            'name' => 'name',
            'value' => 'value2',
            'updated_by' => 2,
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
        ]);

        $user_id1 = Setting::getUpdatedBy('name');
        $user_id2 = Setting::getUpdatedBy('scope.name');

        $this->assertEquals(1, $user_id1);
        $this->assertEquals(2, $user_id2);
    }

    /** @test */
    public function it_cannot_get_updated_by_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class)->create(['name' => 'setting.name', 'value' => 'value', 'updated_by' => 1]);

        $user_id = Setting::getUpdatedBy('setting.name');

        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_gets_updated_at_with_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting1 = factory(Setting::class)->create(['name' => 'name']);
        $setting2 = factory(Setting::class)->create([
            'name' => 'name',
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id
        ]);

        $timestamp1 = Setting::getUpdatedAt('name');
        $timestamp2 = Setting::getUpdatedAt('scope.name');

        $this->assertEquals($setting1->updated_at, $timestamp1);
        $this->assertEquals($setting2->updated_at, $timestamp2);
    }

    /** @test */
    public function it_cannot_get_updated_at_with_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        $setting = factory(Setting::class)->create(['name' => 'setting.name']);

        $timestamp = Setting::getUpdatedAt('setting.name');

        $this->assertEquals($setting->updated_at, $timestamp);
    }

    /** @test */
    public function it_gets_all_values_from_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create();
        factory(Setting::class, 10)->create(['scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id]);

        $unscoped_values = Setting::getScopeValues();
        $scope_values = Setting::getScopeValues('scope');

        $this->assertCount(10, $unscoped_values);
        $this->assertCount(10, $scope_values);
    }

    /** @test */
    public function it_gets_all_values_from_a_scope_in_an_associative_array()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class)->create(['name' => 'name1', 'value' => 'value1']);
        factory(Setting::class)->create(['name' => 'name2', 'value' => 'value2']);

        $values = Setting::getScopeValues();

        $this->assertEquals([
            'name1' => 'value1',
            'name2' => 'value2',
        ], $values);
    }

    /** @test */
    public function it_cannot_get_all_values_from_a_scope_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class, 10)->create(['scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id]);

        $values = Setting::getScopeValues('scope');

        $this->assertEquals(null, $values);
    }

    /** @test */
    public function it_gets_scope_updated_by()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['updated_by' => 1]);
        factory(Setting::class, 10)->create([
            'updated_by' => 2,
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
        ]);

        $unscoped_user_id = Setting::getScopeUpdatedBy();
        $scoped_user_id = Setting::getScopeUpdatedBy('scope');

        $this->assertEquals(1, $unscoped_user_id);
        $this->assertEquals(2, $scoped_user_id);
    }

    /** @test */
    public function it_cannot_get_scope_updated_by_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class, 10)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
            'updated_by' => 1
        ]);

        $user_id = Setting::getScopeUpdatedBy('scope');

        $this->assertEquals(null, $user_id);
    }

    /** @test */
    public function it_gets_scope_updated_at()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $unscoped_settings = factory(Setting::class, 10)->create()->sortBy('updated_at')->first();
        $scoped_settings = factory(Setting::class, 10)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id
        ])->sortBy('updated_at')->first();

        $unscoped_timestamp = Setting::getScopeUpdatedAt();
        $scoped_timestamp = Setting::getScopeUpdatedAt('scope');

        $this->assertEquals($unscoped_settings->updated_at, $unscoped_timestamp);
        $this->assertEquals($scoped_settings->updated_at, $scoped_timestamp);
    }

    /** @test */
    public function it_cannot_get_scope_updated_at_when_scopes_are_disabled()
    {
        $this->app['config']->set('sitesettings.use_scopes', false);
        factory(Setting::class, 10)->create(['scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id]);

        $timestamp = Setting::getScopeUpdatedAt('scope');

        $this->assertEquals(null, $timestamp);
    }

    /** @test */
    public function it_has_multiple_identical_names_in_different_scopes()
    {
        $setting1 = factory(Setting::class)->create([
            'name' => 'name',
            'scope_id' => factory(Scope::class)->create(['name' => 'scope1'])->id,
            'value' => 'value1'
        ]);
        $setting2 = factory(Setting::class)->create([
            'name' => 'name',
            'scope_id' => factory(Scope::class)->create(['name' => 'scope2'])->id,
            'value' => 'value2'
        ]);

        $this->assertEquals('value1', $setting1->value);
        $this->assertEquals('value2', $setting2->value);
    }
}
