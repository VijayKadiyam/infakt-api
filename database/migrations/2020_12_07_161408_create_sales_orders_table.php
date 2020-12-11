<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('order_id')->nullable();
            $table->integer('sku_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('retailer_id')->nullable();
            $table->integer('distributor_id')->nullable();
            $table->integer('invoice_no')->nullable();
            $table->integer('invoice_date')->nullable();
            $table->integer('price_unit')->nullable();
            $table->integer('quantity_placed')->nullable();
            $table->integer('placed_bill_value')->nullable();
            $table->integer('quantity_delivered')->nullable();
            $table->integer('delivered_bill_value')->nullable();
            $table->integer('scheme')->nullable();
            $table->integer('quantity_returned')->nullable();
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
        Schema::dropIfExists('sales_orders');
    }
}
