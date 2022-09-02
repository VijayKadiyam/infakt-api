<?php

use App\ContentDescription;
use Faker\Generator as Faker;

$factory->define(ContentDescription::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'level'=>'level',
        'title'=>'title',
        'description'=>'description',
    ];
});
