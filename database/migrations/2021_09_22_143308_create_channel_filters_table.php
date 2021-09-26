<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChannelFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */     
    public function up()
    {
        Schema::create('channel_filters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('name',100)->nullable();
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
        Schema::dropIfExists('channel_filters');
    }
}
