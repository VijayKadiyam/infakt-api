<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_shops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('shop_type')->nullable();
            $table->string('beat')->nullable();
            $table->string('week_number')->nullable();
            $table->string('outlet_wisdom_code')->nullable();
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
        Schema::dropIfExists('crude_shops');
    }
}
