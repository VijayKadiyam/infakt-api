<?php

use App\Document;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'company_id' => 1,
        'image_path' => 'image_path',
        'description' => 'description',
    ];
});
