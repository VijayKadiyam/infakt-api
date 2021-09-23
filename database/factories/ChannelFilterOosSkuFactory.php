<?php

use App\ChannelFilterOosSku;
use Faker\Generator as Faker;

$factory->define(ChannelFilterOosSku::class, function (Faker $faker) {
    return [
        'sku_id'=>1,
        'status'=>'status'
    ];
});
