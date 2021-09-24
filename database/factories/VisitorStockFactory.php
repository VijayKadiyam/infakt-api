<?php

use App\VisitorStock;
use Faker\Generator as Faker;

$factory->define(VisitorStock::class, function (Faker $faker) {
    return [
        'sku_id' => 1,
        'sku_status' => 'Sku Status',
    ];
});
