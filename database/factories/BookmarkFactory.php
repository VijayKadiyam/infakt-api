<?php

use App\Bookmark;
use Faker\Generator as Faker;

$factory->define(Bookmark::class, function (Faker $faker) {
    return [
        'user_id'=>1,
        'content_id'=>1,
        'is_deleted'=>0,
    ];
});
