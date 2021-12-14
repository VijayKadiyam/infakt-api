<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerDataEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_data_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('retailer_id')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('number', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('product_brought', 100)->nullable();
            $table->string('sample_given', 100)->nullable();
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
        Schema::dropIfExists('customer_data_entries');
    }
}
