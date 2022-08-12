<?php

use Faker\Generator as Faker;
use App\ValueList;

$factory->define(ValueList::class, function (Faker $faker) {
    return [
        'description' =>  'Description 1'
    ];
});
