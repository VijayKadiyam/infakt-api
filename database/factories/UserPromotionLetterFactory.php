<?php

use Faker\Generator as Faker;
use App\UserPromotionLetter;

$factory->define(UserPromotionLetter::class, function (Faker $faker) {
    return [
          'letter'  =>  'Letter 1',
    ];
});
