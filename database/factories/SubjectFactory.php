<?php

use App\Subject;
use Faker\Generator as Faker;

$factory->define(Subject::class, function (Faker $faker) {
    return [
        'name' => "name",
        'is_active' => 1,
    ];
});
