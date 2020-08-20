<?php

use Faker\Generator as Faker;
use App\CompanyDesignation;

$factory->define(CompanyDesignation::class, function (Faker $faker) {
  return [
    'name'  =>  'CEO'
  ];
});
