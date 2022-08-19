<?php

use App\ContentRead;
use Faker\Generator as Faker;

$factory->define(ContentRead::class, function (Faker $faker) {
    return [
        'content_id' => 1,
        'user_id' => 1
    ];
});
