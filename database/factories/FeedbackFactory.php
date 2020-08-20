<?php

use Faker\Generator as Faker;
use App\Feedback;

$factory->define(Feedback::class, function (Faker $faker) {
  return [
    'description' =>  'Feedback 1'
  ];
});
