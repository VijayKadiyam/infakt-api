<?php

use Faker\Generator as Faker;
use App\Inquiry;

$factory->define(Inquiry::class, function (Faker $faker) {
    return [
        'date'  =>  'Date 1'
    ];
});
