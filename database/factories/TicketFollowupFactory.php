<?php

use App\TicketFollowup;
use Faker\Generator as Faker;

$factory->define(TicketFollowup::class, function (Faker $faker) {
    return [
        'ticket_id' => 1,
        'description' => 'description',
        'imagepath1' => 'imagepath1',
        'imagepath2' => 'imagepath2',
        'imagepath3' => 'imagepath3',
        'imagepath4' => 'imagepath4',
        'replied_by_id' => 1,
    ];
});
