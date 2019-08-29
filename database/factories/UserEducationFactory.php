<?php

use Faker\Generator as Faker;
use App\UserEducation;

$factory->define(UserEducation::class, function (Faker $faker) {
    return [
        'examination' =>  'Examination 1',
        'school'      =>  'School 1',
        'passing_year'=>  'Passing Year 1',
        'percent'     =>  'Percent 1'
    ];
});
