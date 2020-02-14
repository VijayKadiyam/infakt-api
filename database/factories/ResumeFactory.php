<?php

use Faker\Generator as Faker;
use App\Resume;

$factory->define(Resume::class, function (Faker $faker) {
    return [
        'name'  =>  'Name 1'
    ];
});
