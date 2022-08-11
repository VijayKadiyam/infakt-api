<?php

use App\AssignmentQuestionOption;
use Faker\Generator as Faker;

$factory->define(AssignmentQuestionOption::class, function (Faker $faker) {
    return [
        'assignment_question_id' => 1,
        'option1' => 'option1',
        'option2' => 'option2',
        'option3' => 'option3',
        'option4' => 'option4',
    ];
});
