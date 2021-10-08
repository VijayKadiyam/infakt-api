<?php

use App\PjpVisitedSupervisor;
use Faker\Generator as Faker;

$factory->define(PjpVisitedSupervisor::class, function (Faker $faker) {
    return [
        'pjp_supervisor_id' => 1,
        'visited_pjp_id' => 1,
        'visited_pjp_market_id' => 1,
        'remarks' => 'Remarks',
        'gps_address' => 'Gps Address',
    ];
});
