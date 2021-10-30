<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePjpSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pjp_supervisors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('user_id')->nullable();
            $table->string('date',100)->nullable();
            $table->integer('actual_pjp_id')->nullable();
            $table->integer('actual_pjp_market_id')->nullable();
            $table->integer('visited_pjp_id')->nullable();
            $table->integer('visited_pjp_market_id')->nullable();
            $table->string('gps_address',100)->nullable();
            $table->string('remarks',100)->nullable();
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
        Schema::dropIfExists('pjp_supervisors');
    }
}
