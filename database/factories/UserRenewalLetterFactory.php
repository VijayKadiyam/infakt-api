<?php

use Faker\Generator as Faker;
use App\UserRenewalLetter;

$factory->define(UserRenewalLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
