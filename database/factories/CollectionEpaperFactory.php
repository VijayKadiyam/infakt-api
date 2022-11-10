<?php

use App\CollectionEpaper;
use Faker\Generator as Faker;

$factory->define(CollectionEpaper::class, function (Faker $faker) {
    return [
        'epaper_collection_id'  => 1,
        'toi_article_id'        => 1,
        'et_article_id'         => 1,
        'is_deleted'            => false,
    ];
});
