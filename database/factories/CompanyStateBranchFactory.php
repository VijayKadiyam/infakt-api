<?php

use Faker\Generator as Faker;
use App\CompanyStateBranch;

$factory->define(CompanyStateBranch::class, function (Faker $faker) {
  return [
    'name'  =>  'Thane'
  ];
});
