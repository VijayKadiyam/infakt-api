<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeUserMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_user_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->nullable();
            $table->string('region')->nullable();
            $table->string('channel')->nullable();
            $table->string('chain_name')->nullable();
            $table->string('billing_code')->nullable();
            $table->string('store_code')->nullable();
            $table->string('store_name')->nullable();
            $table->string('store_address')->nullable();
            $table->string('emp_id')->nullable();
            $table->string('ba_name')->nullable();
            $table->string('location')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('rsm')->nullable();
            $table->string('asm')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('store_type')->nullable();
            $table->string('brand')->nullable();
            $table->string('ba_status')->nullable();
            $table->string('store_status')->nullable();
            $table->string('user_login_id')->nullable();
            $table->string('user_password')->nullable();
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('crude_user_mappings');
    }
}
