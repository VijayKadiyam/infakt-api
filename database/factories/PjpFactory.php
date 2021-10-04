<?php

use App\Pjp;
use Faker\Generator as Faker;

$factory->define(Pjp::class, function (Faker $faker) {
    return [
        'location'=>'location',
        'region'=>'region',
    ];
});
