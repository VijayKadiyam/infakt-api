<?php

use Faker\Generator as Faker;
use App\Salary;

$factory->define(Salary::class, function (Faker $faker) {
    return [
        'month' =>  '01',
        'year'  =>  '2020'
    ];
});
