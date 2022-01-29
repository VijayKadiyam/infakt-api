<?php

use App\Ticket;
use Faker\Generator as Faker;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'title' => 'title',
        'description' => 'description',
        'type' => 'type',
        'status' => 'status',
        'assigned_to_id' => 1,
        'imagepath1' => 'imagepath1',
        'imagepath2' => 'imagepath2',
        'imagepath3' => 'imagepath3',
        'imagepath4' => 'imagepath4',
        'created_by_id' => 1,
    ];
});
