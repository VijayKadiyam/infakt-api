<?php

use App\UserSubject;
use Faker\Generator as Faker;

$factory->define(UserSubject::class, function (Faker $faker) {
    return [
        'user_id'=>   1,
        'subject_id' => 1,
    ];
});
