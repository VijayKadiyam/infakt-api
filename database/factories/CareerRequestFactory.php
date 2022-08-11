<?php

use App\CareerRequest;
use Faker\Generator as Faker;

$factory->define(CareerRequest::class, function (Faker $faker) {
    return [
        'name'          => 'name',
        'email'         => 'email',
        'description'   => 'description',
        'status'        => 'status',
        'remarks'       => 'remarks',
        'is_deleted'    => false,
    ];
});
