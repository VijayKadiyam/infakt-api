<?php

use Faker\Generator as Faker;
use App\CompanyState;

$factory->define(CompanyState::class, function (Faker $faker) {
  return [
    'name'  =>  'Maharashtra'
  ];
});
