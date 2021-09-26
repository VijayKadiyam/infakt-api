<?php

use App\VisitorBa;
use Faker\Generator as Faker;

$factory->define(VisitorBa::class, function (Faker $faker) {
    return [
        'visitor_id' => 1,
        'ba_id' => 1,
        'ba_status' => 'Ba Status',
        'is_grooming' => true,
        'grooming_value' => 100,
        'is_uniform' => true,
        'is_planogram' => true,
        'product_knowledge_value' => 100,
    ];
});
