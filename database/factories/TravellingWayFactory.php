<?php

use Faker\Generator as Faker;
use App\TravellingWay;

$factory->define(TravellingWay::class, function (Faker $faker) {
    return [
        'name'  =>  'One Way'
    ];
});
