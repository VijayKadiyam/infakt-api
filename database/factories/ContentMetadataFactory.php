<?php

use App\ContentMetadata;
use Faker\Generator as Faker;

$factory->define(ContentMetadata::class, function (Faker $faker) {
    return [
        'content_id'    => 1,
        'metadata_type' => 'metadata_type',
        'color_class'   => 'color_class',
        'selected_text' => 'selected_text',
        'annotation'    => 'annotation',
    ];
});
