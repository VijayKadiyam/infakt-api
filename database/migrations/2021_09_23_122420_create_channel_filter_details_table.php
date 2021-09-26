<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelFilterDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_filter_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('ba_1', 100)->nullable();
            $table->string('ba_1_status', 100)->nullable();
            $table->string('ba_2', 100)->nullable();
            $table->string('ba_2_status', 100)->nullable();
            $table->string('ba_3', 100)->nullable();
            $table->string('ba_3_status', 100)->nullable();
            $table->string('ba_4', 100)->nullable();
            $table->string('ba_4_status', 100)->nullable();
            $table->string('brand_block_imagepath')->nullable();
            $table->string('brand_block_description')->nullable();
            $table->boolean('is_tester')->nullable();
            $table->boolean('is_planogram')->nullable();
            $table->boolean('is_grooming')->nullable();
            $table->boolean('is_uniform')->nullable();
            $table->boolean('is_tester_details')->nullable();
            $table->boolean('is_planogram_details')->nullable();
            $table->boolean('is_grooming_details')->nullable();
            $table->boolean('is_uniform_details')->nullable();
            $table->integer('retailer_id')->nullable();
            $table->integer('channel_filter_id')->nullable();
            $table->boolean('is_primary_category')->nullable();
            $table->string('primary_category_imagepath')->nullable();
            $table->boolean('is_secondary_category')->nullable();
            $table->string('secondary_category_imagepath')->nullable();
            $table->string('secondary_category_fsu_imagepath')->nullable();
            $table->string('secondary_category_parasite_imagepath')->nullable();
            $table->string('gandola_imagepath')->nullable();
            $table->boolean('is_ba_training')->nullable();
            $table->string('ba_training_date')->nullable();
            $table->string('ba_training_category')->nullable();
            $table->string('date')->nullable();
            $table->string('visit_feedback')->nullable();
            $table->string('selfie_imagepath')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channel_filter_details');
    }
}
