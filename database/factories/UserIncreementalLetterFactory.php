<?php

use Faker\Generator as Faker;
use App\UserIncreementalLetter;

$factory->define(UserIncreementalLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
