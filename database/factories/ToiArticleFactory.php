<?php

use App\ToiArticle;
use Faker\Generator as Faker;

$factory->define(ToiArticle::class, function (Faker $faker) {
    return [
        'toi_xml_id'    => 1,
        'edition_name'  => 'edition_name',
        'story_id'      => 'story_id',
        'story_date'    => 'story_date',
        'headline'      => 'headline',
        'byline'        => 'byline',
        'category'      => 'category',
        'drophead'      => 'drophead',
        'content'       => 'content',
    ];
});
