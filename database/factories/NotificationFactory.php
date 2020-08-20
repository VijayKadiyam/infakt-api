<?php

use Faker\Generator as Faker;
use App\Notification;

$factory->define(Notification::class, function (Faker $faker) {
    return [
        'notification'  =>  'Notification 1'
    ];
});
