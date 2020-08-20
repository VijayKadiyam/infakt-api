<?php

use Faker\Generator as Faker;
use App\TransportMode;

$factory->define(TransportMode::class, function (Faker $faker) {
  return [
    'name'  =>  'Bike'
  ];
});
