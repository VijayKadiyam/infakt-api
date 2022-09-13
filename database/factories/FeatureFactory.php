<?php

use App\Feature;
use Faker\Generator as Faker;

$factory->define(Feature::class, function (Faker $faker) {
    return [
        'title'=>'title',
        'description'=>'description',
    ];
});
