<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_sales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->string('outlet_name')->nullable();
            $table->string('uid')->nullable();
            $table->string('name_of_person')->nullable();
            $table->string('cell_no')->nullable();
            $table->string('sku')->nullable();
            $table->string('qty')->nullable();
            $table->string('unit_price')->nullable();
            $table->string('bill_value')->nullable();
            $table->string('sku_type')->nullable();
            $table->string('offer')->nullable();
            $table->string('offer_type')->nullable();
            $table->string('offer_amount')->nullable();
            $table->string('total_bill_value')->nullable();
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
        Schema::dropIfExists('crude_sales');
    }
}
