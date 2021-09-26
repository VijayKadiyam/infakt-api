<?php

use App\TrackerSku;
use Faker\Generator as Faker;

$factory->define(TrackerSku::class, function (Faker $faker) {
    return [
        'sku_id' => 1
    ];
});
