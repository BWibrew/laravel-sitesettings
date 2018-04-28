<?php

namespace BWibrew\SiteSettings\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Database\Eloquent\Collection;
use BWibrew\SiteSettings\Tests\Models\Setting;

class CacheTest extends TestCase
{
    /** @test */
    public function it_is_cached_when_registered()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $this->logDBQueries();
        $setting = Setting::register('setting');

        $this->assertCached();
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_is_cached_when_value_is_updated()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'original value']);

        $this->logDBQueries();
        $setting->updateValue('new value');

        $this->assertCached();
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_is_cached_when_name_is_updated()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => 'value']);

        $this->logDBQueries();
        $setting->updateName('new_name');

        $this->assertCached();
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_is_cached_when_scope_is_updated()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['scope' => 'original_scope']);

        $this->logDBQueries();
        $setting->updateScope('new_scope');

        $this->assertCached();
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_value()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'setting value']);

        $this->logDBQueries();
        $value = Setting::getValue('setting_name');

        $this->assertCached();
        $this->assertEquals($value, Cache::get('bwibrew.settings')->first()->value);
        $this->assertCount(1, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_updated_at()
    {
        factory(Setting::class)->create(['name' => 'setting_name']);

        $this->logDBQueries();
        $time = Setting::getUpdatedAt('setting_name');

        $this->assertCached();
        $this->assertEquals($time, Cache::get('bwibrew.settings')->first()->updated_at);
        $this->assertCount(1, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_scope_updated_at()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['scope' => 'scope']);

        $this->logDBQueries();
        $timestamp = Setting::getScopeUpdatedAt('scope');

        $this->assertCached();
        $this->assertEquals($timestamp, Cache::get('bwibrew.settings')->sortBy('updated_at')->first()->updated_at);
        $this->assertCount(1, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_updated_by()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'updated_by' => 1]);

        $this->logDBQueries();
        $user_id = Setting::getUpdatedBy('setting_name');

        $this->assertCached();
        $this->assertInternalType('int', $user_id);
        $this->assertEquals($user_id, Cache::get('bwibrew.settings')->first()->updated_by);
        $this->assertCount(1, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_scope_updated_by()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['scope' => 'scope', 'updated_by' => 1]);

        $this->logDBQueries();
        $user_id = Setting::getScopeUpdatedBy('scope');

        $this->assertCached();
        $this->assertInternalType('int', $user_id);
        $this->assertEquals($user_id, Cache::get('bwibrew.settings')->sortBy('updated_at')->first()->updated_by);
        $this->assertCount(1, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_scope_values()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['scope' => 'scope']);

        $this->logDBQueries();
        $values = Setting::getScopeValues('scope');

        $this->assertCached();
        $this->assertCount(count($values), Cache::get('bwibrew.settings'));
        $this->assertCount(1, DB::getQueryLog());
    }

    protected function logDBQueries()
    {
        DB::connection()->enableQueryLog();
    }

    protected function assertCached()
    {
        $this->assertTrue(Cache::has('bwibrew.settings'));
        $this->assertInstanceOf(Collection::class, Cache::get('bwibrew.settings'));
    }
}
