<?php

use Faker\Generator as Faker;

$factory->define(ContentInfoBoardGrade::class, function (Faker $faker) {
    return [
        'content_info_board_id' => 1,
        'grade_id' => 1
    ];
});
