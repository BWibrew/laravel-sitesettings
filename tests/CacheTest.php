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

        $this->assertTrue(Cache::has('bwibrew.settings'));
        $this->assertInstanceOf(Collection::class, Cache::get('bwibrew.settings'));
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

        $this->assertTrue(Cache::has('bwibrew.settings'));
        $this->assertInstanceOf(Collection::class, Cache::get('bwibrew.settings'));
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

        $this->assertTrue(Cache::has('bwibrew.settings'));
        $this->assertInstanceOf(Collection::class, Cache::get('bwibrew.settings'));
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
        $this->assertCount(2, DB::getQueryLog());
    }

    protected function logDBQueries()
    {
        DB::connection()->enableQueryLog();
    }
}
