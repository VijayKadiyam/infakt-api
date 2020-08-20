<?php

use Faker\Generator as Faker;
use App\Plan;

$factory->define(Plan::class, function (Faker $faker) {
  return [
    'date'  =>  '2019-03-02',
    'plan'  =>  'Worli'
  ];
});
