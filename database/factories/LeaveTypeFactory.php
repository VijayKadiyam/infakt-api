<?php

use Faker\Generator as Faker;
use App\LeaveType;

$factory->define(LeaveType::class, function (Faker $faker) {
    return [
        'name'  =>  'Half Day'
    ];
});
