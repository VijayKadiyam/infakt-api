<?php

use Faker\Generator as Faker;

$factory->define(\App\Mark::class, function (Faker $faker) {
    return [
      'in_lat'   =>  '23.34',
      'in_lng'   =>  '23.34',
      'out_lat'  =>  '34.34',
      'out_lng'  =>  '34.34'
    ];
});
