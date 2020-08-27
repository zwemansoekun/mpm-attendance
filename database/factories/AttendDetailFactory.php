<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\AttendDetail;
use Faker\Generator as Faker;

$factory->define(AttendDetail::class, function (Faker $faker) {
    return [
        'date' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'emp_no' => $faker->numberBetween($min = 1, $max = 50),
        'total_hours' => $faker->randomFloat($nbMaxDecimals =2, $min = 0, $max = 24),
        'am1' => $faker->time($format = 'H:i', $max = 'now'),
        'am2' => $faker->time($format = 'H:i', $max = 'now'),
        'pm1' => $faker->time($format = 'H:i', $max = 'now'),
        'pm2' => $faker->time($format = 'H:i', $max = 'now'),
        'am_leave' => $faker->numberBetween(1,2),
        'pm_leave' => $faker->numberBetween(1,2),
    ];
});
