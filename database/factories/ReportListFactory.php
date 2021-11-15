<?php

use App\ReportList;
use Faker\Generator as Faker;

$factory->define(ReportList::class, function (Faker $faker) {
    return [
        'report_type' => 'Report Type',
        'date' => 'Date',
    ];
});
