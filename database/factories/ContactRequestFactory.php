<?php

use App\ContactRequest;
use Faker\Generator as Faker;

$factory->define(ContactRequest::class, function (Faker $faker) {
    return [
        'name'          => 'name',
        'email'         => 'email',
        'phone_no'      => 'phone_no',
        'interested_in' => 'interested_in',
        'description'   => 'description',
        'status'        => 'status',
        'remarks'       => 'remarks',
        'is_deleted'    => false,
    ];
});
