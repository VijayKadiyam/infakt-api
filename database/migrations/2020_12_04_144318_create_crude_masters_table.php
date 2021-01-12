<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('salesman_name')->nullable();
            $table->string('empl_id')->nullable();
            $table->string('beat_type')->nullable();
            $table->string('day')->nullable();
            $table->string('date')->nullable();
            $table->string('beat_name')->nullable();
            $table->string('town')->nullable();
            $table->string('distributor')->nullable();
            $table->string('sales_officer')->nullable();
            $table->string('area_manager')->nullable();
            $table->string('region')->nullable();
            $table->string('branch')->nullable();
            $table->string('outlet_name')->nullable();
            $table->string('outlet_address')->nullable();
            $table->string('uid')->nullable();
            $table->string('category')->nullable();
            $table->string('class')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('landline_no')->nullable();
            $table->string('mail_id')->nullable();
            $table->string('address')->nullable();
            $table->string('regional')->nullable();
            $table->string('national')->nullable();
            $table->string('email')->nullable();
            $table->integer('which_week')->nullable();
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
        Schema::dropIfExists('crude_masters');
    }
}
