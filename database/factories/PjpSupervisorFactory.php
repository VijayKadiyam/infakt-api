<?php

use App\PjpSupervisor;
use Faker\Generator as Faker;

$factory->define(PjpSupervisor::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'date' => 'date',
        'actual_pjp_id' => 1,
        'actual_pjp_market_id' => 1,
        'visited_pjp_id' => 1,
        'visited_pjp_market_id' => 1,
        'gps_address' => 'gps_address',
        'remarks' => 'remarks',
    ];
});
