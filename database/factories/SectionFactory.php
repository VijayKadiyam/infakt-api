<?php

use App\Section;
use Faker\Generator as Faker;

$factory->define(Section::class, function (Faker $faker) {
    return [
        'name'             => 'name',
        'standard_id'      => 1,
        'is_active'        => true,
    ];
});
