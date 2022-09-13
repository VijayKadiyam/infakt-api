<?php

use App\ContentAssignToRead;
use Faker\Generator as Faker;

$factory->define(ContentAssignToRead::class, function (Faker $faker) {
    return [
        'company_id' => 1,
        'content_id' => 1,
        'collection_id' => 1,
        'classcode_id' => 1,
        'created_by_id' => 1,
    ];
});
