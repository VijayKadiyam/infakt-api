<?php

use App\ContentGrade;
use Faker\Generator as Faker;

$factory->define(ContentGrade::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'grade_id'=>1,
    ];
});
