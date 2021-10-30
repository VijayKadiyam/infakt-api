<?php

use Faker\Generator as Faker;
use App\Requisition;

$factory->define(Requisition::class, function (Faker $faker) {
    return [
        'title' =>  'Title 1'
    ];
});
