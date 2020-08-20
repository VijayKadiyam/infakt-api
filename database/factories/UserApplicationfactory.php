<?php

use Faker\Generator as Faker;
use App\UserApplication;

$factory->define(UserApplication::class, function (Faker $faker) {
  return [
    'company_leave_id'  =>  '1',
    'from_date'         =>  '02-02-2019',
    'to_date'           =>  '05-02-2019',
    'description'       =>  'Sick'
  ];
});
