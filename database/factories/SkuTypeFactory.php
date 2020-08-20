<?php

use Faker\Generator as Faker;

$factory->define(\App\SkuType::class, function (Faker $faker) {
    return [
        'name'  =>  'regular'
    ];
});
