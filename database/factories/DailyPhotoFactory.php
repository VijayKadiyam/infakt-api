<?php

use Faker\Generator as Faker;
use App\DailyPhoto;

$factory->define(DailyPhoto::class, function (Faker $faker) {
    return [
        'image_path'    =>  '1',
        'description'   =>  'Descriptison 1'
    ];
});
