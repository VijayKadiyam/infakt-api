<?php

use Faker\Generator as Faker;
use App\AssetStatus;

$factory->define(AssetStatus::class, function (Faker $faker) {
    return [
        'user_id'         => 1,
        'asset_id'        => 1,
        'status'          => 'Status 1',
        'description'     => 'Description 1',
        'date'            => 'Date 1',
    ];
});
