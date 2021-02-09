<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDamageStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->float('qty')->nullable();
            $table->float('mrp')->nullable();
            $table->string('manufacturing_date')->nullable();
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
        Schema::dropIfExists('damage_stocks');
    }
}
