<?php

use App\ChannelFilterFifo;
use Faker\Generator as Faker;

$factory->define(ChannelFilterFifo::class, function (Faker $faker) {
    return [
        'channel_filter_id'=>1,
        'retailer_id'=>1,
        'date'=>'date',
        'is_sample_article'=>true,
        'is_sellable_article'=>true,
    ];
});
