<?php

use Faker\Generator as Faker;
use App\UserFullFinalLetter;

$factory->define(UserFullFinalLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1'
    ];
});
