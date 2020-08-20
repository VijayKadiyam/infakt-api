<?php

use Faker\Generator as Faker;
use App\VoucherType;

$factory->define(VoucherType::class, function (Faker $faker) {
    return [
        'name'  =>  'xerox'
    ];
});
