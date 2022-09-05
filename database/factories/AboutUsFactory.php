<?php

use App\AboutUs;
use Faker\Generator as Faker;

$factory->define(AboutUs::class, function (Faker $faker) {
    return [
        'tagline'=>'tagline',
        'info'=>'info',
        'info_1'=>'info_1',
        'description'=>'description',
    ];
});
