<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DefaultSetting;
use Faker\Generator as Faker;

$factory->define(DefaultSetting::class, function (Faker $faker) {
    return [
       'ssb_max'=>300000,
       'ssb_paid'=>15000,
    //    'money'=>13.00
    ];
});
