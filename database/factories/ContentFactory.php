<?php

use App\Content;
use Faker\Generator as Faker;

$factory->define(Content::class, function (Faker $faker) {
    return [
        'content_name'     => 'content_name',
        'content_type'     => 'content_type',
        'written_by_id'    => 1,
        'reading_time'     => 'reading_time',
        'content_metadata' => 'content_metadata',
        'easy_content'     => 'easy_content',
        'med_content'      => 'med_content',
        'hard_content'     => 'hard_content',
    ];
});
