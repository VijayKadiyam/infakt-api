<?php

use App\CustomerDataEntry;
use Faker\Generator as Faker;

$factory->define(CustomerDataEntry::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'retailer_id' => 1,
        'name' => 'name',
        'number' => 'numeber',
        'email' => 'email',
        'product_brought' => 'product_brought',
        'sample_given' => 'sample_given',
    ];
});
