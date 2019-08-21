<?php

use Faker\Generator as Faker;
use App\UserTerminationLetter;

$factory->define(UserTerminationLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1'
    ];
});
