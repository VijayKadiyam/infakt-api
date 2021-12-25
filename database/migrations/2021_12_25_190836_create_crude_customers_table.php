<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('store_code')->nullable();
            $table->string('date')->nullable();
            $table->string('no_of_customer')->nullable();
            $table->string('no_of_billed_customer')->nullable();
            $table->string('more_than_two')->nullable();
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
        Schema::dropIfExists('crude_customers');
    }
}
