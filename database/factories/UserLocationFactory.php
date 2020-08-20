<?php

use Faker\Generator as Faker;
use App\UserLocation;

$factory->define(UserLocation::class, function (Faker $faker) {
    return [
        'content' =>  'Hello'
    ];
});
