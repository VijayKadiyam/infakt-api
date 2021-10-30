<?php

use App\Visitor;
use Faker\Generator as Faker;

$factory->define(Visitor::class, function (Faker $faker) {
    return [
        'user_id'=> 1,
        'retailer_id'=> 1
    ];
});
