<?php

use Faker\Generator as Faker;
use App\UserExperienceLetter;

$factory->define(UserExperienceLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
