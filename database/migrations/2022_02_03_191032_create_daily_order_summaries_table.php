<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailyOrderSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_order_summaries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('user_id');
            $table->string('date')->nullable();
            $table->string('opening_stock')->nullable();
            $table->string('received_stock')->nullable();
            $table->string('purchase_returned_stock')->nullable();
            $table->string('sales_stock')->nullable();
            $table->string('returned_stock')->nullable();
            $table->string('closing_stock')->nullable();
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
        Schema::dropIfExists('daily_order_summaries');
    }
}
