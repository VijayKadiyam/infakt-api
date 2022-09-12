<?php

use App\ContentMetadataClasscode;
use Faker\Generator as Faker;

$factory->define(ContentMetadataClasscode::class, function (Faker $faker) {
    return [
        'content_metadata_id' => 1,
        'classcode_id' => 1,
    ];
});
