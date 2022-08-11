<?php

use App\Standard;
use Faker\Generator as Faker;

$factory->define(Standard::class, function (Faker $faker) {
    return [
        'name' => 'name',
        'is_active' => true,
    ];
});
