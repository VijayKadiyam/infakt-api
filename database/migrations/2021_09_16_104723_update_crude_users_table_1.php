<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCrudeUsersTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('crude_users', function (Blueprint $table) {
           $table->string('region',10)->nullable();
           $table->string('channel',10)->nullable();
           $table->string('chain_name',50)->nullable();
           $table->string('billing_code',50)->nullable();
           $table->string('store_code',50)->nullable();
           $table->renameColumn('name','store_name',50)->nullable();
        //    $table->renameColumn('email','store_address',50)->nullable();
           $table->string('ba_name',50)->nullable();
           $table->string('location',100)->nullable();
           $table->string('city',50)->nullable();
           $table->string('state',50)->nullable();
           $table->string('rsm',50)->nullable();
           $table->string('asm',50)->nullable();
           $table->string('supervisor_name',50)->nullable();
           $table->string('store_type',50)->nullable();
           $table->string('brand',50)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('crude_users', function (Blueprint $table) {
            //
        });
    }
}
