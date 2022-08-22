<?php

use App\Notification;
use Faker\Generator as Faker;

$factory->define(Notification::class, function (Faker $faker) {
    return [
        'user_id'=>1,
        'description'=>'description',
    ];
});
