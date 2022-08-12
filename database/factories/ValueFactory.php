<?php

use Faker\Generator as Faker;
use App\Value;

$factory->define(Value::class, function (Faker $faker) {
    return [
        'name'  =>  'Value 1'
    ];
});
