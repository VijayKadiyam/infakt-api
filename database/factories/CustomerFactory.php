<?php

use App\Customer;
use Faker\Generator as Faker;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'date' => 'date',
        'no_of_customer' => 'No Of Customer',
        'no_of_billed_customer' => 'No Of Billed Customer',
        'more_than_two' => 'More Than Two',
    ];
});
