<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Holiday;
use Faker\Generator as Faker;

$factory->define(Holiday::class, function (Faker $faker) {
    return [
        'date' => date('Y-m-d', strtotime("2020-01-01")),
       'description'=> "Holiday Seeder"
    ];
});
