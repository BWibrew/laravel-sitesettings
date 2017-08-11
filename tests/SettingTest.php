<?php

namespace Jamin87\SiteSettings\Tests;

use Jamin87\SiteSettings\Setting;
use Jamin87\SiteSettings\Tests\Models\User;

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
    public function it_can_register_a_new_setting()
    {
        $user = factory(User::class)->create();

        $setting = Setting::register('registered_setting', null, $user);

        $this->assertEquals($setting->name, 'registered_setting');
    }

    /** @test */
    public function it_can_register_a_new_setting_with_a_value()
    {
        $user = factory(User::class)->create();
        Setting::register('new_setting', 'setting value', $user);

        $setting = Setting::where('name', 'new_setting')->first();

        $this->assertEquals($setting->name, 'new_setting');
        $this->assertEquals($setting->value, 'setting value');
    }

    /** @test */
    public function it_can_update_the_setting_name()
    {
        $setting = factory(Setting::class)->create(['name' => 'original_name', 'value' => '']);

        $setting->updateName('new_name');

        $this->assertEquals($setting->name, 'new_name');
    }

    /** @test */
    public function it_can_update_the_setting_value()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'name', 'value' => 'original value']);

        $setting->updateValue('new value', $user);

        $this->assertEquals($setting->value, 'new value');
    }

    /** @test */
    public function it_can_get_the_value()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'setting value']);

        $value = Setting::getValue('setting_name');

        $this->assertEquals($value, 'setting value');
    }

    /** @test */
    public function it_updates_the_user_id_when_updating_a_setting()
    {
        $user = factory(User::class)->create();
        $setting = factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'original value']);
        $this->actingAs($user);

        $setting->updateValue('new value', $user);
        $setting = Setting::where('name', 'setting_name')->first();

        $this->assertEquals($setting->updated_by, $user->id);
    }

    /** @test */
    public function it_sets_the_user_id_when_registering_a_setting()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $setting = Setting::register('setting_name', null, $user);

        $this->assertEquals($setting->updated_by, $user->id);
    }

    /** @test */
    public function it_can_get_the_updated_by_user_id()
    {
        factory(Setting::class)->create(['name' => 'setting_name', 'value' => 'value name', 'updated_by' => 1]);

        $user_id = Setting::getUpdatedBy('setting_name');

        $this->assertEquals($user_id, 1);
    }

    /** @test */
    public function it_can_get_the_updated_timestamp()
    {
        $setting = factory(Setting::class)->create(['name' => 'setting_name']);

        $timestamp = Setting::getWhenUpdated('setting_name');

        $this->assertEquals($timestamp, $setting->updated_at);
    }
}
