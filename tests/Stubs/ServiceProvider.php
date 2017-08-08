<?php
namespace Jamin87\SiteSettings\Tests\Stubs;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(realpath(__DIR__.'/../../src/database/migrations'));
    }
}
