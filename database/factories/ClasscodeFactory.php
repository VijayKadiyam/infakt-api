<?php

use App\Classcode;
use Faker\Generator as Faker;

$factory->define(Classcode::class, function (Faker $faker) {
    return [
        'standard_id' => 1,
        'section_id' => 1,
        'subject_name' => 'subject_name',
        'classcode' => 'classcode',
        'is_active' => true,
        'is_optional' => false,
    ];
});
