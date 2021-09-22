<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrudeTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crude_targets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_id')->nullable();
            $table->string('store_code')->nullable();
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->float('target')->nullable();
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
        Schema::dropIfExists('crude_targets');
    }
}
