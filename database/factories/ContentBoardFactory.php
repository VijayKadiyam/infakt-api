<?php

use App\ContentBoard;
use Faker\Generator as Faker;

$factory->define(ContentBoard::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'board_id'=>1,
    ];
});
