<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelFilterFifosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_filter_fifos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('channel_filter_id')->nullable();
            $table->integer('retailer_id')->nullable();
            $table->string('date')->nullable();
            $table->boolean('is_sample_article')->default(false);
            $table->boolean('is_sellable_article')->default(false);
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
        Schema::dropIfExists('channel_filter_fifos');
    }
}
