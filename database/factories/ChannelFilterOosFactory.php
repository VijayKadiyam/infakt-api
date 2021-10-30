<?php

use App\ChannelFilterOos;
use Faker\Generator as Faker;

$factory->define(ChannelFilterOos::class, function (Faker $faker) {
    return [
        'channel_filter_id' => 1,
        'retailer_id' => 1,
        'date' => 'date',
    ];
});
