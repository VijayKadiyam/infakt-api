<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelFilterOosSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_filter_oos_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_filter_oos_id');
            $table->integer('sku_id')->nullable();
            $table->string('status',50)->nullable();
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
        Schema::dropIfExists('channel_filter_oos_skus');
    }
}
