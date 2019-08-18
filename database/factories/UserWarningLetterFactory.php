<?php

use Faker\Generator as Faker;
use App\UserWarningLetter;

$factory->define(UserWarningLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
