<?php

use App\ChannelFilterDetail;
use Faker\Generator as Faker;

$factory->define(ChannelFilterDetail::class, function (Faker $faker) {
    return [
        'ba_1' => 'BA 1',
        'ba_1_status' => 'BA 1 Status',
        'ba_2' => 'BA 2',
        'ba_2_status' => 'BA 2 Status',
        'ba_3' => 'BA 3',
        'ba_3_status' => 'BA 3 Status',
        'ba_4' => 'BA 4',
        'ba_4_status' => 'BA 4 Status',
        // 'brand_block_imagepath' => '',
        'brand_block_description' => 'Barnd Block Description',
        'is_tester' => true,
        'is_planogram' => true,
        'is_grooming' => true,
        'is_uniform' => true,
        'is_tester_details' => true,
        'is_planogram_details' => true,
        'is_grooming_details' => true,
        'is_uniform_details' => true,
        'retailer_id' => 1,
        'channel_filter_id' => 1,
        'is_primary_category' => true,
        // 'primary_category_imagepath' => '',
        'is_secondary_category' => true,
        // 'secondary_category_imagepath' => '',
        // 'secondary_category_fsu_imagepath' => '',
        // 'secondary_category_parasite_imagepath' => '',
        // 'gandola_imagepath' => '',
        'is_ba_training' => true,
        'ba_training_date' => 'BA Training Date',
        'ba_training_category' => 'BA Training Category',
        'date' => 'Date',
        'visit_feedback' => 'Visit Feedback',
        // 'selfie_imagepath' => '',
    ];
});
