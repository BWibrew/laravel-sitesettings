<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(BWibrew\SiteSettings\Tests\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(BWibrew\SiteSettings\Models\Setting::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
        'scope_id' => 0,
        'value' => $faker->optional(0.9)->sentence(),
        'updated_by' => function () {
            if (mt_rand(0, 1)) {
                return factory(BWibrew\SiteSettings\Tests\Models\User::class)->create()->id;
            } else {
                return;
            }
        },
    ];
});

$factory->define(BWibrew\SiteSettings\Models\SettingWithMedia::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
        'scope_id' => 0,
        'value' => $faker->optional(0.9)->sentence(),
        'updated_by' => function () {
            if (mt_rand(0, 1)) {
                return factory(BWibrew\SiteSettings\Tests\Models\User::class)->create()->id;
            } else {
                return;
            }
        },
    ];
});

$factory->define(BWibrew\SiteSettings\Models\Scope::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
    ];
});
