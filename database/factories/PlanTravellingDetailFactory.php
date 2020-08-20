<?php

use Faker\Generator as Faker;
use App\PlanTravellingDetail;

$factory->define(PlanTravellingDetail::class, function (Faker $faker) {
    return [
        'from'    =>  'Mumbai',
        'to'      =>  'Bangalore',
        'travelling_way_id' =>  '1',
        'transport_mode_id' =>  '1',
        'fare'              =>  '2000'
    ];
});
