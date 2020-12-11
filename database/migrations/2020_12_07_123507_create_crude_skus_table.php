<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('sku_name')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('date')->nullable();
            $table->string('qty')->nullable();
            $table->string('unit')->nullable();
            $table->string('price_per_unit')->nullable();
            $table->string('total_price')->nullable();
            $table->string('sku_type')->nullable();
            $table->string('offer')->nullable();
            $table->string('offer_type')->nullable();
            $table->string('distributor_name')->nullable();
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
        Schema::dropIfExists('crude_skus');
    }
}
