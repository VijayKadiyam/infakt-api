<?php

use Faker\Generator as Faker;
use App\CompanyStateHoliday;

$factory->define(CompanyStateHoliday::class, function (Faker $faker) {
  return [
    'holiday_id'  =>  1
  ];
});
