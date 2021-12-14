<?php

use App\CompetitorData;
use Faker\Generator as Faker;

$factory->define(CompetitorData::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'competitor' => 'competitor',
        'amount' => 'amount',
        'month' => 'month',
        'year' => 'year',
    ];
});
