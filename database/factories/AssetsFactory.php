<?php

use Faker\Generator as Faker;
use App\Asset;

$factory->define(Asset::class, function (Faker $faker) {
    return [
        'asset_name'             => 'Asset1',
        'status'                 => 'Status1',
        'description'            => 'Description1',
        'retailer_id'            => 1,
        'reference_plan_id'       => 1,
    ];
});
