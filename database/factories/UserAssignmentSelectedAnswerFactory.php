<?php

use App\UserAssignmentSelectedAnswer;
use Faker\Generator as Faker;

$factory->define(UserAssignmentSelectedAnswer::class, function (Faker $faker) {
    return [
        "user_id" => 1,
        "assignment_id" => 1,
        "assignment_question_id" => 1,
        "selected_option_sr_no" => 1,
        "is_correct" => false,
        "marks_obtained" => 0,
        "documentpath" => "documentpath",
        "description" => "description",
        "is_deleted" => false,
    ];
});
