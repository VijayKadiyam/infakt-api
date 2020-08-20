<?php

use Faker\Generator as Faker;

$factory->define(\App\Retailer::class, function (Faker $faker) {
    return [
        'name'  =>  'Retailer 1',
        'address' =>  'Mulund'
    ];
});
