<?php

use Faker\Generator as Faker;
use App\Reason;

$factory->define(Reason::class, function (Faker $faker) {
    return [
        'name'  =>  'Reason 1'
    ];
});
