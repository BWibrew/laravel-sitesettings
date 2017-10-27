<?php

namespace BWibrew\SiteSettings\Tests;

use BWibrew\SiteSettings\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use BWibrew\SiteSettings\Tests\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
    public function it_gets_cached_updated_by()
    {
        factory(Setting::class)->create(['name' => 'setting_name']);

        $this->logDBQueries();
        $user_id = Setting::getUpdatedBy('setting_name');

        $this->assertCached();
        $this->assertEquals($user_id, Cache::get('bwibrew.settings')->first()->updated_by);
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
