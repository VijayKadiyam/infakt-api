<?php

use App\AssignmentClasscode;
use Faker\Generator as Faker;

$factory->define(AssignmentClasscode::class, function (Faker $faker) {
    return [
        'assignment_id' => 1,
        'classcode_id' => 1,
        'start_date' => 'start_date',
        'end_date' => 'end_date',
    ];
});
