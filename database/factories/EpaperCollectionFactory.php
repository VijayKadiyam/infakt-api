<?php

use App\EpaperCollection;
use Faker\Generator as Faker;

$factory->define(EpaperCollection::class, function (Faker $faker) {
    return [
        'user_id'             => 1,
        'collection_name'     => 'collection_name',
        'is_deleted'          => false,
    ];
});
