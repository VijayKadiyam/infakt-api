<?php

use Faker\Generator as Faker;
use App\UserReferencePlan;

$factory->define(UserReferencePlan::class, function (Faker $faker) {
    return [
        'day' =>  1,
        'which_week'  =>  1
    ];
});
