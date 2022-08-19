<?php

use App\Assignment;
use Faker\Generator as Faker;

$factory->define(Assignment::class, function (Faker $faker) {
    return [
        'assignment_type' => 'assignment_type',
        'created_by_id' => 1,
        'student_instructions' => 'student_instructions',
        'content_id' => 1,
        'duration' => 'duration',
        'documentpath' => 'documentpath',
        'maximum_marks' => 1,
    ];
});
