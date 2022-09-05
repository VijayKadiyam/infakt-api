<?php

use App\CompanyBoard;
use Faker\Generator as Faker;

$factory->define(CompanyBoard::class, function (Faker $faker) {
    return [
        'board_id' => 1,
    ];
});
