<?php

use App\ContentInfoBoard;
use Faker\Generator as Faker;

$factory->define(ContentInfoBoard::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'board_id'=>1,

    ];
});
