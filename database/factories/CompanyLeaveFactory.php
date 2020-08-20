<?php

use Faker\Generator as Faker;
use App\CompanyLeave;

$factory->define(CompanyLeave::class, function (Faker $faker) {
  return [
      'leave_pattern_id'  =>  '1',
      'name'              =>  'Casual Leave',
      'leaves'            =>  '10'
    ];
});
