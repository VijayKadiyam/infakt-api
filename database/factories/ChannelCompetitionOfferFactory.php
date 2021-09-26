<?php

use App\ChannelCompetitionOffer;
use Faker\Generator as Faker;

$factory->define(ChannelCompetitionOffer::class, function (Faker $faker) {
    return [
        'channel_filter_id' => 1,
        'competitor_name' => 'Competitor Name',
        'description' => 'description',
        'top_articles' => 'Top Articles',
    ];
});
