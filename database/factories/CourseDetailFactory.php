<?php

use Faker\Generator as Faker;
use App\CourseDetail;

$factory->define(CourseDetail::class, function (Faker $faker) {
    return [
        'title' =>  'Title 1',
        'description' =>  'Description 1'
    ];
});
