<?php

use Faker\Generator as Faker;
use App\Asset;

$factory->define(Asset::class, function (Faker $faker) {
    return [
        'asset_name'        => 'Asset1',
        'status'            => 'Status1',
        'description'       => 'Description1'
    ];
});
