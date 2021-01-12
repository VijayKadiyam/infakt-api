<?php

use Faker\Generator as Faker;
use App\Target;

$factory->define(Target::class, function (Faker $faker) {
    return [
        'month' =>  1,
        'year'  =>  1,
        'target'=>  100,
    ];
});
