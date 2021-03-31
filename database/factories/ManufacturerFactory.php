<?php

use Faker\Generator as Faker;
use App\Manufacturer;

$factory->define(Manufacturer::class, function (Faker $faker) {
    return [
        'name'  =>  'M1',
        'email' =>  'E1'
    ];
});
