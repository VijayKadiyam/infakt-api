<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorNPDsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitor_n_p_ds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('visitor_id')->nullable();
            $table->integer('sku_id')->nullable();
            $table->boolean('is_listed')->nullable();
            $table->boolean('is_available')->nullable();
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
        Schema::dropIfExists('visitor_n_p_ds');
    }
}
