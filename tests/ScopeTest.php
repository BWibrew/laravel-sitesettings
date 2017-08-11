<?php

namespace Jamin87\SiteSettings\Tests;

use Jamin87\SiteSettings\Setting;

class ScopeTest extends TestCase
{
    /** @test */
    public function it_has_a_scope()
    {
        $this->app['config']->set('sitesettings.use_scopes', true);
        $setting = factory(Setting::class)->create(['scope' => 'scope_name']);

        $this->assertEquals($setting->scope, 'scope_name');
    }
}
