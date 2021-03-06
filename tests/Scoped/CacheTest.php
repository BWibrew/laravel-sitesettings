<?php

namespace BWibrew\SiteSettings\Tests\Scoped;

use BWibrew\SiteSettings\Models\Scope;
use BWibrew\SiteSettings\Models\Setting;
use BWibrew\SiteSettings\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheTest extends TestCase
{
    /** @test */
    public function it_is_cached_when_scope_is_updated(): void
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['scope_id' => 'original_scope']);

        $this->logDBQueries();
        $setting->updateScope('new_scope');

        $this->assertCount(5, DB::getQueryLog());
        $this->assertCached();

        $setting->scope = $setting->scope()->first()->toArray();
        $this->assertEquals($setting->toArray(), Cache::get('bwibrew.settings')->first()->toArray());
    }

    /** @test */
    public function it_gets_cached_scope_updated_at(): void
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id]);

        $this->logDBQueries();
        $timestamp = Setting::getScopeUpdatedAt('scope');

        $this->assertCached();
        $this->assertEquals($timestamp, Cache::get('bwibrew.settings')->sortBy('updated_at')->first()->updated_at);
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_scope_updated_by(): void
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create([
            'scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id,
            'updated_by' => 1,
        ]);

        $this->logDBQueries();
        $user_id = Setting::getScopeUpdatedBy('scope');

        $this->assertCached();
        $this->assertIsNumeric($user_id);
        $this->assertEquals($user_id, Cache::get('bwibrew.settings')->sortBy('updated_at')->first()->updated_by);
        $this->assertCount(2, DB::getQueryLog());
    }

    /** @test */
    public function it_gets_cached_scope_values(): void
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        factory(Setting::class, 10)->create(['scope_id' => factory(Scope::class)->create(['name' => 'scope'])->id]);

        $this->logDBQueries();
        $values = Setting::getScopeValues('scope');

        $this->assertCached();
        $this->assertCount(3, DB::getQueryLog());
        $this->assertCount(count($values), Cache::get('bwibrew.settings'));
    }
}
