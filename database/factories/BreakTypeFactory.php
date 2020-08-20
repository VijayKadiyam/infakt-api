<?php

use Faker\Generator as Faker;
use App\BreakType;

$factory->define(BreakType::class, function (Faker $faker) {
  return [
    'name'  =>  'PEE'
  ];
});
