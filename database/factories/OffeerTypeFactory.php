<?php

use Faker\Generator as Faker;

$factory->define(\App\OfferType::class, function (Faker $faker) {
    return [
        'name'  =>  'flat'
    ];
});
