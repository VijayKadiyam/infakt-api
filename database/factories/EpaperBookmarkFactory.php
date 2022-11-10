<?php

use App\EpaperBookmark;
use Faker\Generator as Faker;

$factory->define(EpaperBookmark::class, function (Faker $faker) {
    return [
        'user_id'         => 1,
        'toi_article_id'  => 1,
        'et_article_id'   => 1,
        'is_deleted'      => false,
    ];
});
