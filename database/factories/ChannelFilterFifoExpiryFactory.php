<?php

use App\ChannelFilterFifoExpiry;
use Faker\Generator as Faker;

$factory->define(ChannelFilterFifoExpiry::class, function (Faker $faker) {
    return [
        'sku_id'=>1,
        'status'=>'status'
    ];
});
