<?php

use App\Job;
use Faker\Generator as Faker;

$factory->define(Job::class, function (Faker $faker) {
    return [
        'title'=>'title',
        'description'=>'description',
    ];
});
