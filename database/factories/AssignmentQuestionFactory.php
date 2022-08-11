<?php

use App\AssignmentQuestion;
use Faker\Generator as Faker;

$factory->define(AssignmentQuestion::class, function (Faker $faker) {
    return [
        'assignment_id' => 1,
        'description' => 'description',
        'correct_option_sr_no' =>  'correct_option_sr_no',
        'marks' =>  1,
        'negative_marks' => 1,
    ];
});
