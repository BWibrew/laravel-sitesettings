<?php

namespace BWibrew\SiteSettings\Tests;

use Illuminate\Support\Facades\Auth;
use BWibrew\SiteSettings\Tests\Models\User;
use BWibrew\SiteSettings\Tests\Models\Setting;

class SettingTest extends TestCase
{
    /** @test */
    public function it_has_a_name()
    {
        $setting = factory(Setting::class)->create(['name' => 'a_setting_name']);

        $this->assertEquals('a_setting_name', $setting->name);
    }

    /** @test */
    public function it_has_a_value()
    {
        $setting = factory(Setting::class)->create(['value' => 'Hello World!']);

        $this->assertEquals('Hello World!', $setting->value);
    }

    /** @test */
    public function it_registers()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());

        $setting = Setting::register('registered_setting');

        $this->assertEquals('registered_setting', $setting->name);
    }

    /** @test */
    public function it_registers_with_a_value()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        Setting::register('new_setting', 'setting value');

        $setting = Setting::where('name', 'new_setting')->first();

        $this->assertEquals('new_setting', $setting->name);
        $this->assertEquals('setting value', $setting->value);
    }

    /** @test */
    public function it_updates_the_name()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => null]);

        $setting->updateName('new_name');

        $this->assertEquals('new_name', $setting->name);
    }

    /** @test */
    public function it_updates_the_value()
    {
        Auth::shouldReceive('user')->andReturn(factory(User::class)->create());
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'original value']);

        $setting->updateValue('new value');

        $this->assertEquals('new value', $setting->value);
    }

    /** @test */
    public function it_gets_the_value()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'setting value']);

        $value = Setting::getValue('setting_name');

        $this->assertEquals('setting value', $value);
    }

    /** @test */
    public function it_updates_the_user_id()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => 'original value']);
        $this->actingAs($user1);

        $setting->updateValue('new value');

        $this->assertInternalType('int', $setting->updated_by);
        $this->assertEquals($user1->id, $setting->updated_by);

        $this->actingAs($user2);

        $setting->updateName('new_name');

        $this->assertInternalType('int', $setting->updated_by);
        $this->assertEquals($user2->id, $setting->updated_by);
    }

    /** @test */
    public function it_registers_the_user_id()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $setting = Setting::register('setting_name');

        $this->assertEquals($user->id, $setting->updated_by);
    }

    /** @test */
    public function it_gets_the_updater_id()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'value name', 'updated_by' => 1]);

        $user_id = Setting::getUpdatedBy('setting_name');

        $this->assertEquals(1, $user_id);
    }

    /** @test */
    public function it_gets_the_updated_timestamp()
    {
        $setting = factory(Setting::class)->create(['name' => 'setting_name']);

        $timestamp = Setting::getUpdatedAt('setting_name');

        $this->assertEquals($setting->updated_at, $timestamp);
    }

    /** @test */
    public function it_cannot_set_multiple_with_identical_names()
    {
        $this->expectException('Exception');
        $setting1 = factory(Setting::class)->create(['name' => 'name', 'value' => 'value1']);
        $setting2 = factory(Setting::class)->create(['name' => 'name', 'value' => 'value2']);

        $this->assertEquals('value1', $setting1->value);
        $this->assertEquals('value2', $setting2->value);
    }
}
