<?php

use App\CollectionContent;
use Faker\Generator as Faker;

$factory->define(CollectionContent::class, function (Faker $faker) {
    return [
       'collection_id'=>1,
       'content_id'=>1,
       'is_deleted'=>0,
    ];
});
