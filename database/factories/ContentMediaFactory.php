<?php

use App\ContentMedia;
use Faker\Generator as Faker;

$factory->define(ContentMedia::class, function (Faker $faker) {
    return [
        'content_id' => 1,
        'mediapath' => 'mediapath',
    ];
});
