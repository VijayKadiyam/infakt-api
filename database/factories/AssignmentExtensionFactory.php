<?php

use App\AssignmentExtension;
use Faker\Generator as Faker;

$factory->define(AssignmentExtension::class, function (Faker $faker) {
    return [
        'assignment_id' => 1,
        'user_id' => 1,
        'extension_reason' => 'extension_reason',
        'expected_extension_date' => 'expected_extension_date',
        'approved_extension_date' => 'approved_extension_date',
        'is_approved' => false,
    ];
});
