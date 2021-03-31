<?php

use Faker\Generator as Faker;
use App\ShelfAnalysis;

$factory->define(ShelfAnalysis::class, function (Faker $faker) {
    return [
        'description'            => 'Description1',
        'points'                 => 1,
        'image_path_1'           => 'Image 1',
        'image_path_2'           => 'Image 2',
        'image_path_3'           => 'Image 3',
        'image_path_4'           => 'Image 4',
    ];
});
