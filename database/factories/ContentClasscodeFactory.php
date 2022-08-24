<?php

use App\ContentClasscode;
use Faker\Generator as Faker;

$factory->define(ContentClasscode::class, function (Faker $faker) {
    return [
        'content_id' => 1,
        'classcode_id' => 1,
        'created_by_id' => 1,
    ];
});
