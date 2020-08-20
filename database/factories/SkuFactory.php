<?php

use Faker\Generator as Faker;


$factory->define(\App\Sku::class, function (Faker $faker) {
    return [
        'name'  =>  'Santoor'
    ];
});
