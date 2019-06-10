<?php

use Faker\Generator as Faker;
use App\GeolocatorUserLocation;

$factory->define(GeolocatorUserLocation::class, function (Faker $faker) {
    return [
        'lat' =>  '13.2',
        'long'  =>  '24.34'
    ];
});
