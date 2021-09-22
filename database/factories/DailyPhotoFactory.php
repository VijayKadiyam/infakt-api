<?php

use Faker\Generator as Faker;
use App\DailyPhoto;

$factory->define(DailyPhoto::class, function (Faker $faker) {
    return [
        'image_path'    =>  '1',
        'description'   =>  'Descriptison 1',
        'title'   =>  'title 1',
        'date'   =>  'date 1',
    ];
});
