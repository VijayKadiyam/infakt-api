<?php

use Faker\Generator as Faker;
use App\UserReference;

$factory->define(UserReference::class, function (Faker $faker) {
    return [
      'name'          =>  'Name 1',
      'company_name'  =>  'Company Name 1',
      'designation'   =>  'Designation 1',
      'contact_number'=>  'Contact Number 1'
    ];
});
