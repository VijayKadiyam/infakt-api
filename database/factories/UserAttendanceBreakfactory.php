<?php

use Faker\Generator as Faker;
use App\UserAttendanceBreak;

$factory->define(UserAttendanceBreak::class, function (Faker $faker) {
return [
    'user_attendance_id'  =>  1,
    'break_type_id'       =>  '2',
    'start_time'          =>  '11.20',
    'end_time'            =>  '20.10'
  ];
});
