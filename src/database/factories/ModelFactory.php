<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Jamin87\SiteSettings\Tests\Models\User::class, function (Faker\Generator $faker) {
    static $password;
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Jamin87\SiteSettings\Setting::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
        'scope' => $faker->optional(0.2)->word,
        'value' => $faker->optional(0.1)->sentence(),
        'updated_by' => function () {
            if (mt_rand(0, 1)) {
                return factory(Jamin87\SiteSettings\Tests\Models\User::class)->create()->id;
            } else {
                return null;
            }
        },
    ];
});
