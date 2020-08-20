<?php

use Faker\Generator as Faker;
use App\PlanActual;

$factory->define(PlanActual::class, function (Faker $faker) {
    return [
        'status'  =>  1,
        'details' =>  'Done',
    ];
});
