<?php

use App\ContentLockClasscode;
use Faker\Generator as Faker;

$factory->define(ContentLockClasscode::class, function (Faker $faker) {
    return [
        'content_id' => 1,
        'classcode_id' => 1,
        'level' => 'level',
    ];
});
