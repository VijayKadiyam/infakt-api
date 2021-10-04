<?php

use App\PjpMarket;
use Faker\Generator as Faker;

$factory->define(PjpMarket::class, function (Faker $faker) {
    return [
        'pjp_id' => 1,
        'market_name' => 'Market Name',
        'gps_address' => 'Gps Address',
    ];
});
