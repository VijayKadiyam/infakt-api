<?php

use App\Board;
use Faker\Generator as Faker;

$factory->define(Board::class, function (Faker $faker) {
    return [
        'name' => "name",
        'is_active' => 1,
    ];
});
