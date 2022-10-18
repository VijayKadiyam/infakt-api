<?php

use App\UserAssignmentTiming;
use Faker\Generator as Faker;

$factory->define(UserAssignmentTiming::class, function (Faker $faker) {
    return [
        'company_id' => 1,
        'user_id' => 1,
        'assignment_id' => 1,
        'user_assignment_id' => 1,
        'timestamp' => 'timestamp',
    ];
});
