<?php

use App\UserAssignment;
use Faker\Generator as Faker;

$factory->define(UserAssignment::class, function (Faker $faker) {
    return [
        "user_id" => 1,
        "assignment_id" => 1,
        "submission_date" => "submission_date",
        "score" => 1,
        "documentpath" => "documentpath",
        'is_deleted' => 0,
    ];
});
