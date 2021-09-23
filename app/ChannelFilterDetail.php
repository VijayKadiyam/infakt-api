<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChannelFilterDetail extends Model
{
    protected $fillable = [
        'ba_1',
        'ba_1_status',
        'ba_2',
        'ba_2_status',
        'ba_3',
        'ba_3_status',
        'ba_4',
        'ba_4_status',
        'brand_block_imagepath',
        'brand_block_description',
        'is_tester',
        'is_planogram',
        'is_grooming',
        'is_uniform',
        'is_tester_details',
        'is_planogram_details',
        'is_grooming_details',
        'is_uniform_details',
        'retailer_id',
        'channel_filter_id',
        'is_primary_category',
        'primary_category_imagepath',
        'is_secondary_category',
        'secondary_category_imagepath',
        'secondary_category_fsu_imagepath',
        'secondary_category_parasite_imagepath',
        'gandola_imagepath',
        'is_ba_training',
        'ba_training_date',
        'ba_training_category',
        'date',
        'visit_feedback',
        'selfie_imagepath',
    ];
}
