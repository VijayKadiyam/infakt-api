<?php

use Faker\Generator as Faker;
use App\DamageStock;

$factory->define(DamageStock::class, function (Faker $faker) {
    return [
        'qty'                 => 1.0,
        'mrp'                 => 100.0,
        'manufacturing_date'  => 'Date 1',
        'sku_id'              => 1

    ];
});
