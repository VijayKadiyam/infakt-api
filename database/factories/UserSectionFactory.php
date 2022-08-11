<?php

use App\UserSection;
use Faker\Generator as Faker;

$factory->define(UserSection::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'section_id' => 1,
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'is_active' => 1,
        'is_deleted' => 0,
    ];
});
