<?php

use Faker\Generator as Faker;
use App\Order;

$factory->define(Order::class, function (Faker $faker) {
    return [
        'distributor_id'  =>  1,
        'user_id'         =>  2,
        'retailer_id'     =>  3,
        'status'          =>  0 
    ];
});
