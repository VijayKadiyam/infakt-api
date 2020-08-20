<?php

use Faker\Generator as Faker;
use App\UserSale;

$factory->define(UserSale::class, function (Faker $faker) {
  return [
    'date'        =>  \Carbon\Carbon::now()->format('Y-m-d'),
    'amount'      =>  '20',
    'customer_name' =>  'Vijay',
    'phone_no'      =>  '978787778787'
  ];
});
