<?php

use App\ContentCategory;
use Faker\Generator as Faker;

$factory->define(ContentCategory::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'category_id'=>1,
    ];
});
