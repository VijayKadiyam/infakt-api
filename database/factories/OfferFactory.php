<?php

use Faker\Generator as Faker;

$factory->define(\App\Offer::class, function (Faker $faker) {
    return [
        'offer' =>  '50'
    ];
});
