<?php

use App\UserTimestamp;
use Faker\Generator as Faker;

$factory->define(UserTimestamp::class, function (Faker $faker) {
    return [
        'user_id'=>1,
        'timestamp'=>'timestamp',
        'event'=>'event',
    ];
});
