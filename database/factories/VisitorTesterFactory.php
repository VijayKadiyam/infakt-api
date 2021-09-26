<?php

use App\VisitorTester;
use Faker\Generator as Faker;

$factory->define(VisitorTester::class, function (Faker $faker) {
    return [
        'sku_id' => 1,
        'sku_status' => 'Sku Status',
    ];
});
