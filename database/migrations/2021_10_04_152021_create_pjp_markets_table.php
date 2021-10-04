<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePjpMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjp_markets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('pjp_id')->nullable();
            $table->string('market_name')->nullable();
            $table->string('gps_address')->nullable();
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
        Schema::dropIfExists('pjp_markets');
    }
}
