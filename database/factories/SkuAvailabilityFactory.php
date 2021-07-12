<?php

use Faker\Generator as Faker;
use App\SkuAvailability;

$factory->define(SkuAvailability::class, function (Faker $faker) {
    return [
        'date'            => 'Date 1',
    ];
});
