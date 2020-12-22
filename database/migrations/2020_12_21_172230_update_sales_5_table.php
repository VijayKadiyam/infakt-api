<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSales5Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('order_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('unit_price')->nullable();
            $table->float('bill_value')->nullable();
            $table->string('sku_type')->nullable();
            $table->float('offer')->nullable();
            $table->string('offer_type')->nullable();
            $table->float('offer_amount')->nullable();
            $table->float('total_bill_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
