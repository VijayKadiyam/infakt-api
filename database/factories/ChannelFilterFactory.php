<?php

use App\ChannelFilter;
use Faker\Generator as Faker;

$factory->define(ChannelFilter::class, function (Faker $faker) {
    return [
        "name"=>'Name 1'
    ];
});
