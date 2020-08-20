<?php

use Faker\Generator as Faker;
use App\AllowanceType;

$factory->define(AllowanceType::class, function (Faker $faker) {
  return [
    'name'  =>  'HO',
    'amount'=>  '200'
  ];
});
