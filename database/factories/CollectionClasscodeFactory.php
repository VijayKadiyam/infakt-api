<?php

use App\CollectionClasscode;
use Faker\Generator as Faker;

$factory->define(CollectionClasscode::class, function (Faker $faker) {
    return [
        'collection_id' => 1,
        'classcode_id' => 1,
    ];
});
