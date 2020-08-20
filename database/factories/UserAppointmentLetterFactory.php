<?php

use Faker\Generator as Faker;
use App\UserAppointmentLetter;

$factory->define(UserAppointmentLetter::class, function (Faker $faker) {
    return [
        'letter'  =>  'Letter 1',
    ];
});
