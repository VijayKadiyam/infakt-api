<?php

use App\Tracker;
use Faker\Generator as Faker;

$factory->define(Tracker::class, function (Faker $faker) {
    return [
        'retailer_id' => 1,
        'customer_name' => 'Customer Name',
        'contact_no' => 'Contact no',
        'email_id' => 'Email id',
        'tracker_type' => 'Sample',
    ];
});
