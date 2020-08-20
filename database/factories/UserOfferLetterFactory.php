<?php

use Faker\Generator as Faker;
use App\UserOfferLetter;

$factory->define(UserOfferLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
