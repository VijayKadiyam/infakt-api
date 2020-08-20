<?php

use Faker\Generator as Faker;
use App\Company;

$factory->define(Company::class, function (Faker $faker) {
  return [
   'name'   => $faker->name,
   'email'  =>  $faker->email,
   'phone'  =>  $faker->phoneNumber,
   'address'=>  $faker->address,
  ];
});
