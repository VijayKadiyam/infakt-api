<?php

use Faker\Generator as Faker;
use App\OrderDetail;

$factory->define(OrderDetail::class, function (Faker $faker) {
    return [
        'sku_id'        =>  1,
        'qty'           =>  1,
        'value'         =>  100,
        'qty_delivered' =>  1
    ];
});
