<?php

use App\BookmarkClasscode;
use Faker\Generator as Faker;

$factory->define(BookmarkClasscode::class, function (Faker $faker) {
    return [
        'bookmark_id' => 1,
        'classcode_id' => 1,
    ];
});
