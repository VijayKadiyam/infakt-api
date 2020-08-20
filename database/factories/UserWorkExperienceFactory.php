<?php

use Faker\Generator as Faker;
use App\UserWorkExperience;

$factory->define(UserWorkExperience::class, function (Faker $faker) {
    return [
        'company_name'  =>  'Company Name 1',
        'from'          =>  'From 1',
        'to'            =>  'To 1',
        'designation'   =>  'Designation 1',
        'uan_no'        =>  'UAN NO 1',
        'esic_no'       =>  'ESIC No 1'
    ];
});
