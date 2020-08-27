<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Setting;
use Faker\Generator as Faker;

$factory->define(Setting::class, function (Faker $faker) {
    return [
        'money' => 13,
        'am' => 5,
        'pm' => 15,
        'create_month' => '2019/07'
    ];
});
