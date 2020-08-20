<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTravellingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_travelling_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('plan_id');
            $table->string('from');
            $table->string('to');
            $table->integer('travelling_way_id');
            $table->integer('transport_mode_id');
            $table->integer('fare')->nullable();
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
        Schema::dropIfExists('plan_travelling_details');
    }
}
