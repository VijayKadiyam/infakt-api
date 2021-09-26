<?php

use App\VisitorNpd;
use Faker\Generator as Faker;

$factory->define(VisitorNpd::class, function (Faker $faker) {
    return [
        'sku_id' => 1,
        'is_listed' => true,
        'is_available' => true,
    ];
});
