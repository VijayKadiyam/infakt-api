<?php

use Faker\Generator as Faker;

$factory->define(\App\RetailerCategory::class, function (Faker $faker) {
    return [
        'name'  =>  'Cat 1'
    ];
});
