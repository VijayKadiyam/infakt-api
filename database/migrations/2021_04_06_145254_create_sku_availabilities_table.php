<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkuAvailabilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sku_availabilities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('reference_plan_id')->nullable();
            $table->integer('retailer_id')->nullable();
            $table->integer('sku_id')->nullable();
            $table->boolean('is_available')->nullable();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('sku_availabilities');
    }
}
